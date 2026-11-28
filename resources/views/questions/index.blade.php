<x-app-layout title="Questions" subtitle="Create, edit, retire">
    <section class="card stack">
        <div class="spread">
            <div>
                <h2>Audit questions</h2>
                <p class="sub">{{ $totalCount }} questions, {{ $activeCount }} active</p>
            </div>
            <a class="btn primary" href="{{ route('questions.create') }}">Add question</a>
        </div>

        <p class="note">
            Uploading the question workbook replaces changes made here until version control arrives in Week 3.
        </p>

        {{-- Filters are a plain GET form, so they stay in the URL and in the page links. --}}
        <form method="GET" action="{{ route('questions.index') }}" class="row">
            <input type="search" name="search" value="{{ request('search') }}" class="grow"
                   placeholder="Search wording or code" aria-label="Search questions">

            <select name="department" aria-label="Filter by section">
                <option value="">All sections</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->department_id }}" @selected(request('department') == $department->department_id)>{{ $department->name }}</option>
                @endforeach
            </select>

            <select name="status" aria-label="Filter by status">
                <option value="">All statuses</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <button class="btn" type="submit">Filter</button>

            @if (request()->filled('search') || request()->filled('department') || request()->filled('status'))
                <a class="btn link" href="{{ route('questions.index') }}">Clear</a>
            @endif
        </form>

        <div class="tablewrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Section</th>
                        <th>Question</th>
                        <th>Answers</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($questions as $question)
                        <tr @class(['retired' => ! $question->is_active])>
                            <td class="mono">{{ $question->displayCode() }}</td>
                            <td>{{ $question->department?->name ?? '—' }}</td>
                            <td class="q">
                                {{ $question->question_text }}<br>
                                <span class="sub">{{ $question->heading?->name }} › {{ $question->subHeading?->name }}</span>
                            </td>
                            <td>{{ $answerOptions[$question->answer_options] ?? $question->answer_options }}</td>
                            <td>
                                <span @class(['pill', 'bad' => $question->priority === 'critical'])>
                                    {{ $priorities[$question->priority] ?? $question->priority }}
                                </span>
                            </td>
                            <td>
                                <span @class(['pill', 'good' => $question->is_active, 'warn' => ! $question->is_active])>
                                    {{ $question->is_active ? 'Active' : 'Retired' }}
                                </span>
                            </td>
                            <td>
                                <div class="acts">
                                    <a class="btn sm" href="{{ route('questions.edit', $question->question_id) }}">Edit</a>

                                    <form method="POST" action="{{ route($question->is_active ? 'questions.retire' : 'questions.reactivate', $question->question_id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn sm" type="submit">{{ $question->is_active ? 'Retire' : 'Reactivate' }}</button>
                                    </form>

                                    {{-- Answered questions can only be retired, so past audits keep them. --}}
                                    @unless ($question->answers_exists)
                                        <form method="POST" action="{{ route('questions.destroy', $question->question_id) }}"
                                              data-confirm="Delete question {{ $question->displayCode() }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn sm" type="submit">Delete</button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">No questions match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $questions->links() }}
    </section>
</x-app-layout>
