<?php

namespace App\Http\Controllers;

use App\Models\AnswerIaudit;
use App\Models\Company;
use App\Models\CrtTrapLocationIaudit;
use App\Models\DepartmentIaudit;
use App\Models\EfkIAudit;
use App\Models\OtherCrtIAudit;
use App\Models\OtherEfkIAudit;
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
                'first_name'  => $user->first_name,
                'last_name'  => $user->last_name,
                'email' => $user->email,
                'title' => $user->title,
            ],
        ]);
    }

    public function companies(Request $request)
    {
        $companies = Company::query()
            ->whereHas('fleets.ships') // ensure relevant data exists
            ->with(['fleets' => function ($q) {
                $q->select('id', 'name', 'mnemonic', 'cruise_company_id')
                    ->whereHas('ships') // only fleets that have ships
                    ->with(['ships' => function ($sq) {
                        $sq->select('id', 'name', 'mnemonic', 'fleet_id')
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
                        'mnemonic' => $fleet->mnemonic,
                        'ships' => $fleet->ships->map(fn($ship) => [
                            'id'   => $ship->id,
                            'name' => $ship->name,
                            'mnemonic' => $ship->mnemonic,
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
        // === Eager load all relations to prevent N+1 queries ===
        $departments = DepartmentIaudit::with([
            'templates.questions' => function ($q) {
                $q->with(['category', 'heading', 'subHeading', 'ncs']);
            },
        ])->get();

        // === Transform data ===
        $result = $departments->map(function ($department) {
            $details = [];

            // Flatten all questions from all templates for this department
            $questions = $department->templates
                ->flatMap(fn($template) => $template->questions)
                ->filter();

            if ($questions->isEmpty()) {
                return [
                    'department_id'   => $department->department_id,
                    'department_name' => $department->name,
                    'details'         => [],
                ];
            }

            // Group questions by heading
            $questionsByHeading = $questions->groupBy(fn($q) => optional($q->heading)->heading_id);

            foreach ($questionsByHeading as $headingId => $qs) {
                $heading = [
                    'heading_id'   => $headingId,
                    'heading_name' => optional($qs->first()->heading)->name ?? 'Untitled Heading',
                    'subheadings'  => [],
                ];

                // Group by subheading
                $questionsBySub = $qs->groupBy(fn($q) => optional($q->subHeading)->subheading_id);

                foreach ($questionsBySub as $subId => $subQs) {
                    $subHeadingName = optional($subQs->first()->subHeading)->name ?? 'General';

                    // Group by category
                    $questionsByCategory = $subQs->groupBy(fn($q) => optional($q->category)->category_id);

                    $categories = $questionsByCategory->map(function ($categoryQs, $categoryId) {
                        $categoryName = optional($categoryQs->first()->category)->name ?? 'Uncategorized';

                        // Map questions with NCS data
                        $questionsArr = $categoryQs->map(function ($q) {
                            return [
                                'question_id'       => $q->question_id,
                                'question_text'     => $q->question_text,
                                'information_text'  => $q->information_text,
                                'question_ncs'      => $q->ncs,
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
                        'subheading_name' => $subHeadingName,
                        'categories'      => $categories,
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

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
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
            'file'        => ['nullable'], // single or multiple
            'file.*'      => [
                'file',
                'mimes:jpg,jpeg,png,gif,svg,pdf,doc,docx,xlsx,xls,txt,mp4,mov,avi,mkv',
                'max:10240',
            ],
        ]);

        $savedFiles = [];

        DB::beginTransaction();
        try {
            // Create answer
            $answer = AnswerIaudit::create([
                'user_id'     => $user->id,
                'ship_id'     => $validated['ship_id'],
                'question_id' => $validated['question_id'],
                'answer'      => $validated['answer'],
                'note'        => $validated['note'] ?? null,
                'files'       => null,
            ]);

            // Normalize to array (handles both single & multiple files)
            $files = $request->file('file');
            if ($files) {
                $files = is_array($files) ? $files : [$files];

                foreach ($files as $file) {
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
            Log::error("Audit submit failed: " . $e->getMessage());

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

    public function trapLocations()
    {
        $departments = CrtTrapLocationIaudit::select(
            'department_id',
            'department_name',
            'deck',
            'main_section',
            'sub_section',
            'trap_location',
            'trap_type'
        )->get()
            ->groupBy('department_id');

        $result = $departments->map(function ($deptRows, $deptId) {
            $departmentName = optional($deptRows->first())->department_name;

            // Group by deck
            $decks = $deptRows->groupBy('deck')->map(function ($deckRows, $deckName) {
                $mainSections = $deckRows->groupBy('main_section')->map(function ($mainRows, $mainName) {
                    $subSections = $mainRows->groupBy('sub_section')->map(function ($subRows, $subName) {
                        $traps = $subRows->map(function ($row) {
                            return [
                                'trap_location' => $row->trap_location,
                                'trap_type'     => $row->trap_type,
                            ];
                        })->values();

                        return [
                            'sub_section'    => $subName,
                            'trap_locations' => $traps,
                        ];
                    })->values();

                    return [
                        'main_section' => $mainName,
                        'sub_sections' => $subSections,
                    ];
                })->values();

                return [
                    'deck'          => $deckName,
                    'main_sections' => $mainSections,
                ];
            })->values();

            return [
                'department_id'   => $deptId,
                'department_name' => $departmentName,
                'details'         => $decks,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }

    public function efkLocations()
    {
        $departments = EfkIAudit::select(
            'department_id',
            'department_name',
            'deck',
            'area',
            'location',
            'type'
        )->get()
            ->groupBy('department_id');

        $result = $departments->map(function ($deptRows, $deptId) {
            $departmentName = optional($deptRows->first())->department_name;

            // Group by deck
            $decks = $deptRows->groupBy('deck')->map(function ($deckRows, $deckName) {
                $areas = $deckRows->groupBy('area')->map(function ($areaRows, $areaName) {
                    $locations = $areaRows->map(function ($row) {
                        return [
                            'location' => $row->location,
                            'type'     => $row->type,
                        ];
                    })->values();

                    return [
                        'area'       => $areaName,
                        'locations'  => $locations,
                    ];
                })->values();

                return [
                    'deck'  => $deckName,
                    'areas' => $areas,
                ];
            })->values();

            return [
                'department_id'   => $deptId,
                'department_name' => $departmentName,
                'details'         => $decks,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }

    public function otherCrtLocations()
    {
        $rows = OtherCrtIAudit::select(
            'id',
            'other_crt_ref',
            'other_crt_main_heading',
            'other_crt_ordinal',
            'other_crt_sub_heading',
            'other_crt_category',
            'other_crt_sub_category',
            'other_crt_type',
            'other_crt_type_mnemonic',
            'other_crt_compliance',
            'other_crt_logic',
            'other_crt_non_compliance_text',
            'other_crt_i_info',
            'other_crt_usph_ref',
            'other_crt_ship_san_ref',
            'other_crt_anvia_ref',
            'other_crt_mpi_ref'
        )
            ->orderBy('other_crt_ordinal')
            ->get()
            ->groupBy('other_crt_main_heading');

        $result = $rows->map(function ($mainRows, $mainHeading) {

            return [
                'ref'          => optional($mainRows->first())->other_crt_ref,
                'main_heading' => $mainHeading,

                'sub_headings' => $mainRows
                    ->groupBy('other_crt_sub_heading')
                    ->map(function ($subHeadingRows, $subHeading) {

                        return [
                            'sub_heading' => $subHeading,

                            'categories' => $subHeadingRows
                                ->groupBy('other_crt_category')
                                ->map(function ($catRows, $category) {

                                    return [
                                        'category' => $category,

                                        'sub_categories' => $catRows
                                            ->groupBy('other_crt_sub_category')
                                            ->map(function ($subRows, $subCategory) {

                                                return [
                                                    'sub_category' => $subCategory,

                                                    'items' => $subRows->map(function ($row) {
                                                        return [
                                                            'id' => $row->id,
                                                            'ordinal' => $row->other_crt_ordinal,

                                                            'type' => [
                                                                'name' => $row->other_crt_type,
                                                                'mnemonic' => $row->other_crt_type_mnemonic,
                                                            ],

                                                            'compliance' => $row->other_crt_compliance,
                                                            'logic' => $row->other_crt_logic,

                                                            'content' => [
                                                                'non_compliance_text' => $row->other_crt_non_compliance_text,
                                                                'i_info'              => $row->other_crt_i_info,
                                                            ],

                                                            'references' => [
                                                                'usph'     => $row->other_crt_usph_ref,
                                                                'ship_san' => $row->other_crt_ship_san_ref,
                                                                'anvia'    => $row->other_crt_anvia_ref,
                                                                'mpi'      => $row->other_crt_mpi_ref,
                                                            ],
                                                        ];
                                                    })->values(),
                                                ];
                                            })->values(),
                                    ];
                                })->values(),
                        ];
                    })->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'count'   => $rows->flatten()->count(),
            'data'    => $result,
        ]);
    }

    public function otherEfkLocations()
    {
        $rows = OtherEfkIAudit::select(
            'id',
            'other_efk_ref',
            'other_efk_main_heading',
            'other_efk_ordinal',
            'other_efk_sub_heading',
            'other_efk_sub_category',
            'other_efk_type',
            'other_efk_type_mnemonic',
            'other_efk_compliance',
            'other_efk_logic',
            'other_efk_non_compliance_text',
            'other_efk_i_info',
            'other_efk_usph_ref',
            'other_efk_ship_san_ref',
            'other_efk_anvia_ref',
            'other_efk_mpi_ref'
        )
            ->orderBy('other_efk_ordinal')
            ->get()
            ->groupBy('other_efk_main_heading');

        $result = $rows->map(function ($mainRows, $mainHeading) {

            return [
                'ref'          => optional($mainRows->first())->other_efk_ref,
                'main_heading' => $mainHeading,

                'sub_headings' => $mainRows
                    ->groupBy('other_efk_sub_heading')
                    ->map(function ($subHeadingRows, $subHeading) {

                        return [
                            'sub_heading' => $subHeading,

                            'categories' => $subHeadingRows
                                ->groupBy('other_efk_category')
                                ->map(function ($catRows, $category) {

                                    return [
                                        'category' => $category,

                                        'sub_categories' => $catRows
                                            ->groupBy('other_efk_sub_category')
                                            ->map(function ($subRows, $subCategory) {

                                                return [
                                                    'sub_category' => $subCategory,

                                                    'items' => $subRows->map(function ($row) {
                                                        return [
                                                            'id' => $row->id,
                                                            'ordinal' => $row->other_efk_ordinal,

                                                            'type' => [
                                                                'name' => $row->other_efk_type,
                                                                'mnemonic' => $row->other_efk_type_mnemonic,
                                                            ],

                                                            'compliance' => $row->other_efk_compliance,
                                                            'logic'      => $row->other_efk_logic,

                                                            'content' => [
                                                                'non_compliance_text' => $row->other_efk_non_compliance_text,
                                                                'i_info'              => $row->other_efk_i_info,
                                                            ],

                                                            'references' => [
                                                                'usph'     => $row->other_efk_usph_ref,
                                                                'ship_san' => $row->other_efk_ship_san_ref,
                                                                'anvia'    => $row->other_efk_anvia_ref,
                                                                'mpi'      => $row->other_efk_mpi_ref,
                                                            ],
                                                        ];
                                                    })->values(),
                                                ];
                                            })->values(),
                                    ];
                                })->values(),
                        ];
                    })->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'count'   => $rows->flatten()->count(),
            'data'    => $result,
        ]);
    }
}
