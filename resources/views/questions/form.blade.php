{{-- Add and edit share this form. $question->exists tells them apart. --}}
@php($editing = $question->exists)

<x-app-layout
    :title="$editing ? 'Edit question ' . $question->displayCode() : 'Add question'"
    :subtitle="$editing ? 'Changes apply as soon as you save' : 'A code such as NEW001 is assigned when you save'">

    <section class="card">
        <form method="POST" class="stack"
              action="{{ $editing ? route('questions.update', $question->question_id) : route('questions.store') }}">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <div class="form">
                {{-- The template is fixed once a question exists; it only needs choosing when there is more than one. --}}
                @unless ($editing)
                    @if ($templates->count() > 1)
                        <label class="field">Template
                            <select name="template" required>
                                @foreach ($templates as $template)
                                    <option value="{{ $template }}" @selected(old('template') === $template)>{{ $template }}</option>
                                @endforeach
                            </select>
                        </label>
                    @else
                        <input type="hidden" name="template" value="{{ $templates->first() }}">
                    @endif
                @endunless

                <label class="field">Section
                    <select name="department_id" required>
                        <option value="">Choose a section</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->department_id }}" @selected(old('department_id', $question->department_id) == $department->department_id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="field">Heading
                    <select name="heading_id" required>
                        <option value="">Choose a heading</option>
                        @foreach ($headings as $heading)
                            <option value="{{ $heading->heading_id }}" @selected(old('heading_id', $question->heading_id) == $heading->heading_id)>{{ $heading->name }}</option>
                        @endforeach
                    </select>
                </label>

                {{-- Grouped by heading, so the right subheading is easy to find without any script. --}}
                <label class="field">Subheading
                    <select name="subheading_id" required>
                        <option value="">Choose a subheading</option>
                        @foreach ($headings as $heading)
                            <optgroup label="{{ $heading->name }}">
                                @foreach ($heading->subCategories as $subheading)
                                    <option value="{{ $subheading->subheading_id }}" @selected(old('subheading_id', $question->subheading_id) == $subheading->subheading_id)>{{ $subheading->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </label>

                {{-- Type to pick an existing category, or enter a new name to create one. --}}
                <label class="field">Category
                    <input name="category" type="text" value="{{ old('category', $question->category?->name) }}" list="category-names" maxlength="191" required>
                </label>
                <datalist id="category-names">
                    @foreach ($categories as $name)
                        <option value="{{ $name }}">
                    @endforeach
                </datalist>

                <label class="field wide">Question
                    <textarea name="question_text" minlength="8" maxlength="2000" required>{{ old('question_text', $question->question_text) }}</textarea>
                </label>

                <label class="field">Answer options
                    <select name="answer_options" required>
                        @foreach ($answerOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('answer_options', $question->answer_options) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="field">Priority
                    <select name="priority" required>
                        @foreach ($priorities as $value => $label)
                            <option value="{{ $value }}" @selected(old('priority', $question->priority) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="row">
                <button class="btn primary" type="submit">{{ $editing ? 'Save changes' : 'Add question' }}</button>
                <a class="btn" href="{{ route('questions.index') }}">Cancel</a>
            </div>
        </form>
    </section>
</x-app-layout>
