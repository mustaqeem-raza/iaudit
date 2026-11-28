<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Models\CategoryIaudit;
use App\Models\DepartmentIaudit;
use App\Models\HeadingIaudit;
use App\Models\QuestionIaudit;
use App\Services\QuestionReferenceData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * Admin screens for the question bank (Week 1 of the database management
 * module): list, add, edit, retire, reactivate and delete questions.
 *
 * Each action is a plain form post that redirects with a flash message.
 * - Delete hides the row (QuestionIaudit uses SoftDeletes) and is refused
 *   for a question that has answers: retire it instead.
 * - Until Week 3 the workbook import stays the master copy and can overwrite
 *   what is changed here; the list page says so.
 */
class QuestionController extends Controller
{
    /** Status filter: query value => label. */
    private const STATUSES = [
        'active'  => 'Active',
        'retired' => 'Retired',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $department = $request->query('department');
        $status = $request->query('status');

        $questions = QuestionIaudit::questionRows()
            ->with(['department', 'heading', 'subHeading'])
            ->withExists('answers')
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('question_text', 'like', "%{$search}%")
                ->orWhere('short_code', 'like', "%{$search}%")))
            ->when($department, fn ($query) => $query->where('department_id', $department))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'retired', fn ($query) => $query->where('is_active', false))
            ->orderBy('department_id')
            ->orderBy('ordinal')
            ->orderBy('question_id')
            ->paginate(15)
            ->withQueryString();

        return view('questions.index', [
            'questions'     => $questions,
            'departments'   => DepartmentIaudit::orderBy('name')->get(['department_id', 'name']),
            'statuses'      => self::STATUSES,
            'answerOptions' => QuestionIaudit::ANSWER_OPTIONS,
            'priorities'    => QuestionIaudit::PRIORITIES,
            'totalCount'    => QuestionIaudit::questionRows()->count(),
            'activeCount'   => QuestionIaudit::questionRows()->active()->count(),
        ]);
    }

    public function create(): View
    {
        return $this->form(new QuestionIaudit([
            'answer_options' => array_key_first(QuestionIaudit::ANSWER_OPTIONS),
            'priority'       => array_key_first(QuestionIaudit::PRIORITIES),
        ]));
    }

    public function store(QuestionRequest $request): RedirectResponse
    {
        try {
            $question = DB::transaction(function () use ($request) {
                $references = new QuestionReferenceData();
                $template = $request->validated('template');

                return QuestionIaudit::create($this->editableFields($request, $references) + [
                    'question_id' => $references->nextQuestionId(),
                    'template'    => $template,
                    'short_code'  => $this->nextScreenCode($template),
                    'row_type'    => 'question',
                    // Last in the template's order; withTrashed so a deleted question's place is not reused.
                    'ordinal'     => (int) QuestionIaudit::withTrashed()->where('template', $template)->max('ordinal') + 1,
                    'is_active'   => true,
                ]);
            });
        } catch (Throwable $e) {
            Log::error('Question create failed', ['exception' => $e]);

            return back()->withInput()->with('error', 'The question could not be saved. Please try again.');
        }

        return redirect()->route('questions.index')
            ->with('success', "Question {$question->displayCode()} added.");
    }

    public function edit(int $id): View
    {
        return $this->form(QuestionIaudit::questionRows()->with('category')->findOrFail($id));
    }

    public function update(QuestionRequest $request, int $id): RedirectResponse
    {
        $question = QuestionIaudit::questionRows()->findOrFail($id);

        try {
            $changed = DB::transaction(function () use ($request, $question) {
                $question->fill($this->editableFields($request, new QuestionReferenceData()));

                return $question->isDirty() && $question->save();
            });
        } catch (Throwable $e) {
            Log::error('Question update failed', ['question_id' => $id, 'exception' => $e]);

            return back()->withInput()->with('error', 'The question could not be saved. Please try again.');
        }

        return redirect()->route('questions.index')->with('success', $changed
            ? "Question {$question->displayCode()} saved."
            : "No changes to question {$question->displayCode()}.");
    }

    public function retire(int $id): RedirectResponse
    {
        return $this->setActive($id, false);
    }

    public function reactivate(int $id): RedirectResponse
    {
        return $this->setActive($id, true);
    }

    /**
     * Hides the question (soft delete). Its row, id and report text stay, so a
     * phone that cached it can still submit and the id is never reused.
     */
    public function destroy(int $id): RedirectResponse
    {
        $question = QuestionIaudit::questionRows()->withExists('answers')->findOrFail($id);

        if ($question->answers_exists) {
            return back()->with('error', "Question {$question->displayCode()} has answers, so it can't be deleted. Retire it instead.");
        }

        $question->delete();

        return back()->with('success', "Question {$question->displayCode()} deleted.");
    }

    /** The add and edit pages share one form. */
    private function form(QuestionIaudit $question): View
    {
        return view('questions.form', [
            'question'      => $question,
            'templates'     => QuestionIaudit::templateCodes(),
            'departments'   => DepartmentIaudit::orderBy('name')->get(['department_id', 'name']),
            'headings'      => HeadingIaudit::with(['subCategories' => fn ($query) => $query->orderBy('name')])
                ->orderBy('name')
                ->get(),
            'categories'    => CategoryIaudit::orderBy('name')->pluck('name'),
            'answerOptions' => QuestionIaudit::ANSWER_OPTIONS,
            'priorities'    => QuestionIaudit::PRIORITIES,
        ]);
    }

    /** The fields the form edits, with the category name resolved to an id (created if new). */
    private function editableFields(QuestionRequest $request, QuestionReferenceData $references): array
    {
        return [
            'department_id'  => $request->validated('department_id'),
            'heading_id'     => $request->validated('heading_id'),
            'subheading_id'  => $request->validated('subheading_id'),
            'category_id'    => $references->categoryId($request->validated('category')),
            'question_text'  => $request->validated('question_text'),
            'answer_options' => $request->validated('answer_options'),
            'priority'       => $request->validated('priority'),
        ];
    }

    private function setActive(int $id, bool $active): RedirectResponse
    {
        $question = QuestionIaudit::questionRows()->findOrFail($id);
        $question->update(['is_active' => $active]);

        return back()->with('success', "Question {$question->displayCode()} " . ($active ? 'reactivated.' : 'retired.'));
    }

    /**
     * The next on-screen code in a template: NEW001, NEW002… Counts deleted
     * questions too, so a code is never handed out twice.
     */
    private function nextScreenCode(string $template): string
    {
        $prefix = QuestionIaudit::SCREEN_CODE_PREFIX;

        $highest = QuestionIaudit::withTrashed()
            ->where('template', $template)
            ->where('short_code', 'like', $prefix . '%')
            ->pluck('short_code')
            ->map(fn (string $code) => preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $code, $match) ? (int) $match[1] : 0)
            ->max() ?? 0;

        return $prefix . str_pad((string) ($highest + 1), 3, '0', STR_PAD_LEFT);
    }
}
