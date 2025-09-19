<?php

namespace App\Http\Controllers;

use App\Models\AnswerIaudit;
use App\Models\Company;
use App\Models\DepartmentIaudit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ApiController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if (! Auth::attempt($validated)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('mobile-' . Str::uuid())->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
            ],
        ]);
    }

    public function companies(Request $request)
    {
        $companies = Company::query()
            ->whereHas('fleets.ships') // ensure relevant data exists
            ->with(['fleets' => function ($q) {
                $q->select('id', 'name', 'cruise_company_id')
                    ->whereHas('ships') // only fleets that have ships
                    ->with(['ships' => function ($sq) {
                        $sq->select('id', 'name', 'fleet_id')
                            ->orderBy('name');
                    }])
                    ->orderBy('name');
            }])
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Shape to: [{ id, name, fleets:[{ id, name, ships:[{id, name}]}]}]
        $payload = $companies->map(function ($company) {
            return [
                'id'     => $company->id,
                'name'   => $company->name,
                'fleets' => $company->fleets->map(function ($fleet) {
                    return [
                        'id'    => $fleet->id,
                        'name'  => $fleet->name,
                        'ships' => $fleet->ships->map(fn($ship) => [
                            'id'   => $ship->id,
                            'name' => $ship->name,
                        ])->values(),
                    ];
                })->values(),
            ];
        });

        return response()->json($payload);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function questions()
    {
        $questions = DepartmentIaudit::with([
            'templates.questions.category',
            'templates.questions.heading',
            'templates.questions.subHeading'
        ])->get();

        $result = $questions->map(function ($department) {
            $details = [];

            // Flatten questions across all templates in this department
            $questions = $department->templates->flatMap->questions;

            // Group by heading id (null-safe)
            $questionsByHeading = $questions->groupBy(fn($q) => optional($q->heading)->heading_id);

            foreach ($questionsByHeading as $headingId => $qs) {
                $heading = [
                    'heading_id'   => $headingId,
                    'heading_name' => optional($qs->first()->heading)->name,
                    'subheadings'  => []
                ];

                // Group by subheading id within this heading (null-safe)
                $questionsBySub = $qs->groupBy(fn($q) => optional($q->subHeading)->subheading_id);

                foreach ($questionsBySub as $subId => $subQs) {
                    // Now group by category inside the subheading (null-safe)
                    $questionsByCategory = $subQs->groupBy(fn($q) => optional($q->category)->category_id);

                    $categories = $questionsByCategory->map(function ($categoryQs, $categoryId) {
                        $categoryName = optional($categoryQs->first()->category)->name;

                        $questionsArr = $categoryQs->map(function ($q) {
                            return [
                                'question_id'      => $q->question_id,
                                'question_text'    => $q->question_text,
                                'information_text' => $q->information_text,
                            ];
                        })->values();

                        return [
                            'category_id'   => $categoryId,
                            'category_name' => $categoryName,
                            'questions'     => $questionsArr,
                        ];
                    })->values();

                    $heading['subheadings'][] = [
                        'subheading_id'   => $subId,
                        'subheading_name' => optional($subQs->first()->subHeading)->name,
                        'category'      => $categories,
                    ];
                }

                $details[] = $heading;
            }

            return [
                'department_id'   => $department->department_id,
                'department_name' => $department->name,
                'details'         => $details,
            ];
        })->values();

        return response()->json($result);
    }

    public function submitAudit(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Validate request
        $validated = $request->validate([
            'ship_id'     => ['required', 'integer', 'exists:ships,id'],
            'question_id' => ['required', 'integer', 'exists:questions_iaudit,question_id'],
            'answer'      => ['required', Rule::in(['Yes', 'No', 'N/A'])],
            'note'        => ['nullable', 'string', 'max:2000'],
            'file'        => ['nullable', 'array'], // must be an array if multiple
            'file.*'      => [
                'file',
                'mimes:jpg,jpeg,png,gif,svg,pdf,doc,docx,xlsx,xls,txt,mp4,mov,avi,mkv',
                'max:10240',
            ],
        ]);

        $savedFiles = [];

        DB::beginTransaction();
        try {
            // Create answer first
            $answer = AnswerIaudit::create([
                'user_id'     => $user->id,
                'ship_id'     => $validated['ship_id'],
                'question_id' => $validated['question_id'],
                'answer'      => $validated['answer'],
                'note'        => $validated['note'] ?? null,
                'files'       => null,
            ]);

            // Handle file uploads if provided
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    if (! $file->isValid()) {
                        Log::warning("Invalid file skipped for answer {$answer->id}");
                        continue;
                    }

                    $path = $file->store("audits/{$answer->id}", 'public');

                    $savedFiles[] = [
                        'path'          => $path,
                        'url'           => Storage::disk('public')->url($path),
                        'original_name' => $file->getClientOriginalName(),
                        'size'          => $file->getSize(),
                        'mime'          => $file->getClientMimeType(),
                    ];
                }

                if (! empty($savedFiles)) {
                    $answer->update(['files' => $savedFiles]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Audit submit failed: ".$e->getMessage());

            return response()->json([
                'message' => 'Failed to submit audit. Please try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'Audit answer submitted successfully.',
            'data'    => [
                'id'          => $answer->id,
                'user_id'     => $answer->user_id,
                'ship_id'     => $answer->ship_id,
                'question_id' => $answer->question_id,
                'answer'      => $answer->answer,
                'note'        => $answer->note,
                'files'       => $answer->files ?? [],
                'created_at'  => $answer->created_at,
            ],
        ], 201);
    }
}
