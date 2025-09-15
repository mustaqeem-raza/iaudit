<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DepartmentIaudit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

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
        // First 5 companies (by name) that have at least one fleet with at least one ship
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
            ->limit(5)
            ->get();

        // Shape to: [{ name, fleets:[{ name, ships:[{name}]}]}]
        $payload = $companies->map(function ($company) {
            return [
                'name'   => $company->name,
                'fleets' => $company->fleets->map(function ($fleet) {
                    return [
                        'name'  => $fleet->name,
                        'ships' => $fleet->ships->map(fn($ship) => ['name' => $ship->name])->values(),
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

    public function departments()
    {
        $departments = DepartmentIaudit::with([
            'templates.questions.category',
            'templates.questions.heading',
            'templates.questions.subHeading'
        ])->get();

        $result = $departments->map(function ($department) {
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
}
