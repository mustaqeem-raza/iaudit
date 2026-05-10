<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\CrtTrapLocationIaudit;
use App\Models\DepartmentIaudit;
use App\Models\EfkIAudit;
use App\Models\Ship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Browsershot\Browsershot;

class AuditReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with(['user', 'ship'])->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('ship', fn($s) => $s->where('name', 'like', "%{$search}%")
                        ->orWhere('mnemonic', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($shipId = $request->get('ship_id')) {
            $query->where('ship_id', $shipId);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $audits = $query->paginate(15)->withQueryString();
        $users  = User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $ships  = Ship::orderBy('name')->get(['id', 'name', 'mnemonic']);

        return view('audit-listing', compact('audits', 'users', 'ships'));
    }

    public function staticReport()
    {
        return view('static-audit-pdf-report');
    }

    public function showReport($id = null)
    {
        $auditData = $id ? $this->buildAuditData($id) : $this->emptyAuditData();
        return view('static-audit-pdf-report', compact('auditData'));
    }

    public function showPDFReport($id = null)
    {
        $auditData = $id ? $this->buildAuditData($id) : $this->emptyAuditData();
        $html = view('static-audit-pdf-report', compact('auditData'))->render();

        $pdf = $this->renderPdf($html);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="audit-report-' . ($id ?? 'preview') . '.pdf"');
    }

    public function downloadPdf($id = null)
    {
        $auditData = $id ? $this->buildAuditData($id) : $this->emptyAuditData();
        $html = view('audit-pdf-report', compact('auditData'))->render();

        $pdf = $this->renderPdf($html);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="audit-report-' . ($id ?? 'export') . '.pdf"');
    }

    /**
     * Render HTML to PDF.
     *
     * Uses PDFShift (Chromium-based HTTP API) when PDFSHIFT_API_KEY is set
     * — required on Hostinger Premium where Node/Chromium aren't available.
     * Falls back to Browsershot locally for dev when no key is configured.
     *
     * Viewport is fixed at 1241x1755 to match the design's `--page-w`/`--page-h`,
     * so the many `clamp(_, vw, _)` rules in pdf-style.css resolve to their
     * intended sizes instead of collapsing to the `min` value at A4 width.
     */
    private function renderPdf(string $html): string
    {
        $apiKey = config('services.pdfshift.key');

        if ($apiKey) {
            $response = Http::withBasicAuth('api', $apiKey)
                ->timeout(120)
                ->asJson()
                ->acceptJson()
                ->post('https://api.pdfshift.io/v3/convert/pdf', [
                    'source'    => $html,
                    'format'    => 'A4',
                    'margin'    => '0',
                    'use_print' => true,
                    'viewport'  => '1241x1755',
                    'sandbox'   => false,
                ]);

            if (! $response->successful()) {
                abort(502, 'PDF generation failed: ' . $response->body());
            }

            return $response->body();
        }

        return Browsershot::html($html)
            ->setNodeBinary('/usr/bin/node')
            ->setNpmBinary('/usr/bin/npm')
            ->setChromePath('/usr/bin/google-chrome')
            ->noSandbox()
            ->disableJavascript()
            ->setOption('waitUntil', 'load')
            ->windowSize(1241, 1755)
            ->timeout(120)
            ->format('A4')
            ->pdf();
    }

    /**
     * Load an audit with all relations needed by the report template
     * and shape it into a flat array the view can consume.
     */
    private function buildAuditData(int $id): array
    {
        $audit = Audit::with([
            'user',
            'ship.fleet.company',
            'answers',
        ])->findOrFail($id);

        // Index answers by question_id so we can attach to every question
        $answersByQuestion = $audit->answers->keyBy('question_id');

        // Build the full departments → headings → subheadings → categories → questions tree
        // (matches the same shape used by the mobile app's GET /api/questions)
        $departments = DepartmentIaudit::with([
            'templates.questions' => function ($q) {
                $q->with(['heading', 'subHeading', 'category', 'ncs']);
            },
        ])->get();

        $departmentsTree = $departments->map(function ($dept) use ($answersByQuestion) {
            $allQuestions = $dept->templates
                ->flatMap(fn($t) => $t->questions)
                ->filter()
                ->unique('question_id');

            $byHeading = $allQuestions->groupBy(fn($q) => optional($q->heading)->heading_id);

            $headings = $byHeading->map(function ($hQuestions, $hId) use ($answersByQuestion) {
                $headingName = optional($hQuestions->first()->heading)->name ?? 'General';

                $bySubheading = $hQuestions->groupBy(fn($q) => optional($q->subHeading)->subheading_id);

                $subheadings = $bySubheading->map(function ($subQuestions, $subId) use ($answersByQuestion) {
                    $subName       = optional($subQuestions->first()->subHeading)->name ?? 'General';
                    $criteriaText  = $subQuestions->pluck('information_text')->filter()->first();

                    $byCategory = $subQuestions->groupBy(fn($q) => optional($q->category)->category_id);

                    $categories = $byCategory->map(function ($catQuestions, $catId) use ($answersByQuestion) {
                        $catName = optional($catQuestions->first()->category)->name ?? 'General';

                        $questions = $catQuestions->map(function ($q) use ($answersByQuestion) {
                            $a = $answersByQuestion->get($q->question_id);
                            return [
                                'question_id'      => $q->question_id,
                                'question_text'    => $q->question_text,
                                'information_text' => $q->information_text,
                                'ncs'              => $q->ncs ?? collect(),
                                'answer'           => $a?->answer,
                                'note'             => $a?->note,
                                'files'            => $a?->files ?? [],
                                'is_answered'      => (bool) $a,
                            ];
                        })->values();

                        return [
                            'category_id'   => $catId,
                            'category_name' => $catName,
                            'questions'     => $questions,
                        ];
                    })->values();

                    $answeredQuestions = $subQuestions->filter(
                        fn($q) => $answersByQuestion->has($q->question_id)
                    );
                    $noAnswerQuestions = $answeredQuestions->filter(
                        fn($q) => optional($answersByQuestion->get($q->question_id))->answer === 'No'
                    );

                    return [
                        'subheading_id'   => $subId,
                        'subheading_name' => $subName,
                        'criteria_text'   => $criteriaText,
                        'categories'      => $categories,
                        'has_answers'     => $answeredQuestions->isNotEmpty(),
                        'non_compliant'   => $noAnswerQuestions->map(function ($q) use ($answersByQuestion) {
                            $a = $answersByQuestion->get($q->question_id);
                            return [
                                'question_id'   => $q->question_id,
                                'question_text' => $q->question_text,
                                'note'          => $a?->note,
                                'files'         => $a?->files ?? [],
                                'ncs'           => $q->ncs ?? collect(),
                            ];
                        })->values(),
                    ];
                })->values();

                return [
                    'heading_id'   => $hId,
                    'heading_name' => $headingName,
                    'subheadings'  => $subheadings,
                    'has_answers'  => $subheadings->contains(fn($s) => $s['has_answers']),
                ];
            })->values();

            return [
                'department_id'   => $dept->department_id,
                'department_name' => $dept->name,
                'headings'        => $headings,
                'has_answers'     => $headings->contains(fn($h) => $h['has_answers']),
            ];
        })->values();

        // Reference data: trap & EFK locations, grouped by department
        $trapLocations = CrtTrapLocationIaudit::all()
            ->groupBy('department_id')
            ->map(fn($rows) => [
                'department_name' => optional($rows->first())->department_name,
                'rows'            => $rows->values(),
            ]);

        $efkLocations = EfkIAudit::all()
            ->groupBy('department_id')
            ->map(fn($rows) => [
                'department_name' => optional($rows->first())->department_name,
                'rows'            => $rows->values(),
            ]);

        $answers = $audit->answers;

        return [
            'audit_id'        => $audit->id,
            'reference'       => $audit->reference_number,
            'status'          => $audit->status,
            'score'           => $audit->score,
            'submitted_at'    => $audit->created_at,

            'user_name'       => $audit->user
                ? trim("{$audit->user->first_name} {$audit->user->last_name}")
                : 'N/A',
            'user_email'      => $audit->user?->email ?? '',
            'user_title'      => $audit->user?->title ?? '',

            'ship_name'       => $audit->ship?->name ?? 'N/A',
            'ship_mnemonic'   => $audit->ship?->mnemonic ?? '',
            'fleet_name'      => $audit->ship?->fleet?->name ?? 'N/A',
            'company_name'    => $audit->ship?->fleet?->company?->name ?? 'N/A',

            'total_answers'   => $answers->count(),
            'yes_count'       => $answers->where('answer', 'Yes')->count(),
            'no_count'        => $answers->where('answer', 'No')->count(),
            'na_count'        => $answers->where('answer', 'N/A')->count(),

            // Full hierarchy used by the report body
            'departments'     => $departmentsTree,
            'trap_locations'  => $trapLocations,
            'efk_locations'   => $efkLocations,
        ];
    }

    private function emptyAuditData(): array
    {
        return [
            'audit_id'       => null,
            'reference'      => 'PREVIEW',
            'status'         => 'preview',
            'score'          => null,
            'submitted_at'   => now(),
            'user_name'      => 'N/A',
            'user_email'     => '',
            'user_title'     => '',
            'ship_name'      => 'N/A',
            'ship_mnemonic'  => '',
            'fleet_name'     => 'N/A',
            'company_name'   => 'N/A',
            'total_answers'  => 0,
            'yes_count'      => 0,
            'no_count'       => 0,
            'na_count'       => 0,
            'departments'    => collect(),
            'trap_locations' => collect(),
            'efk_locations'  => collect(),
        ];
    }

    // public function previewPdf()
    // {
    //     $data = [
    //         'company' => 'Oceanic Cruises',
    //         'fleet' => 'Pacific Fleet',
    //         'ship' => 'Sea Explorer',
    //         'audit_date' => '2025-08-14',
    //         'auditor' => 'John Doe',
    //         'questions' => [
    //             ['code' => 'Q1', 'question' => 'Is the kitchen clean?', 'answer' => 'Yes'],
    //             ['code' => 'Q2', 'question' => 'Are safety protocols followed?', 'answer' => 'No'],
    //             ['code' => 'Q3', 'question' => 'Is documentation up to date?', 'answer' => 'Yes'],
    //         ]
    //     ];

    //     $pdf = Pdf::loadView('audit-report-bootstrap', compact('data'));
    //     return $pdf->stream('audit-report-bootstrap.pdf'); // Opens in browser
    // }
}
