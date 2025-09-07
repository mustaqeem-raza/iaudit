<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class WorkbookImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Department'     => new DepartmentImport(),
            'Category'       => new CategoryImport(),
            'Heading'        => new HeadingImport(),
            'Sub_Heading'    => new SubHeadingImport(),
            'Template'       => new TemplateImport(),
            'Template_Ref'   => new TemplateRefImport(),
            'Question'       => new QuestionImport(),
            'Question_NC'    => new QuestionNcImport(),
            'Criteria'       => new CriteriaImport(),
            'Text_Box'       => new TextBoxImport(),
        ];
    }
}
