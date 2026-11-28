<?php

namespace App\Http\Requests;

use App\Models\QuestionIaudit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for the admin question form, shared by add (POST) and edit (PUT).
 *
 * Section (department), heading and subheading are picked from existing
 * rows; the category is a free-text name, found or created on save like the
 * workbook import does. The template is chosen only when adding: it is part
 * of the question's identity ([template, short_code]) and never changes.
 */
class QuestionRequest extends FormRequest
{
    /** Any logged-in user, until Week 4 brings roles. */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [
            'department_id'  => ['required', 'integer', Rule::exists('departments_iaudit', 'department_id')],
            'heading_id'     => ['required', 'integer', Rule::exists('headings_iaudit', 'heading_id')],
            // The subheading must sit under the chosen heading.
            'subheading_id'  => ['required', 'integer', Rule::exists('sub_headings_iaudit', 'subheading_id')
                ->where('heading_id', (int) $this->input('heading_id'))],
            'category'       => ['required', 'string', 'max:191'],
            'question_text'  => ['required', 'string', 'min:8', 'max:2000'],
            'answer_options' => ['required', Rule::in(array_keys(QuestionIaudit::ANSWER_OPTIONS))],
            'priority'       => ['required', Rule::in(array_keys(QuestionIaudit::PRIORITIES))],
        ];

        if ($this->isMethod('post')) {
            $rules['template'] = ['required', 'string', Rule::in(QuestionIaudit::templateCodes()->all())];
        }

        return $rules;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'subheading_id.exists' => 'Choose a subheading that belongs to the selected heading.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'department_id' => 'section',
            'heading_id'    => 'heading',
            'subheading_id' => 'subheading',
            'question_text' => 'question wording',
        ];
    }
}
