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
        $departments = DepartmentIaudit::query()
            ->with([
                'templates' => function ($t) {
                    $t->with([
                        'templateRef',
                        'textBoxes',
                        'questions',
                    ]);
                },
            ])
            ->get();
        return response()->json($departments);

        // Pull everything we need in one go
        // $departments = DepartmentIaudit::query()
        //     ->with([
        //         'templates.questions' => function ($q) {
        //             $q->select([
        //                 'question_id',
        //                 'reference_id',
        //                 'category_id',
        //                 'subheading_id',
        //                 'question_text',
        //                 'information_text',
        //                 'heading_id'
        //             ])->with([
        //                 'category:category_id,name as category_name',
        //                 'subHeading:subheading_id,subheading_name',
        //             ]);
        //         },
        //     ])
        //     ->get(['department_id', 'name as department_name']);

        // // Transform into Department → Category → Subcategory → Questions
        // $payload = $departments->map(function ($dept) {
        //     // gather all questions for this department across all templates
        //     $questions = $dept->templates->flatMap->questions;

        //     // group by category, then subcategory
        //     $categories = $questions
        //         ->groupBy(fn($q) => optional($q->category)->category_id)
        //         ->map(function ($catGroup) {
        //             $category = optional($catGroup->first()->category);

        //             $subcategories = $catGroup
        //                 ->groupBy(fn($q) => optional($q->subHeading)->subheading_id)
        //                 ->map(function ($subGroup) {
        //                     $sub = optional($subGroup->first()->subHeading);

        //                     return [
        //                         'subheading_id'   => $sub->subheading_id ?? null,
        //                         'subheading_name' => $sub->subheading_name ?? null,
        //                         'questions'       => $subGroup->map(function ($q) {
        //                             return [
        //                                 'question_id'      => $q->question_id,
        //                                 'question_text'    => $q->question_text,
        //                                 'information_text' => $q->information_text,
        //                                 // include if you want:
        //                                 'heading_id'       => $q->heading_id,
        //                                 'category_id'      => $q->category_id,
        //                                 'subheading_id'    => $q->subheading_id,
        //                             ];
        //                         })->values(),
        //                     ];
        //                 })->values();

        //             return [
        //                 'category_id'   => $category->category_id ?? null,
        //                 'category_name' => $category->category_name ?? 'Uncategorized',
        //                 'subcategories' => $subcategories,
        //             ];
        //         })->values();

        //     return [
        //         'department_id'   => $dept->department_id,
        //         'department_name' => $dept->department_name,
        //         'categories'      => $categories,
        //     ];
        // });
        // return response()->json($payload);
    }
}
