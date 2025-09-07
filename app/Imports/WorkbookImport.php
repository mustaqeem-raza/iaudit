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
            'Sub_Category'    => new SubCategoryImport(),
            'Template_Ref'    => new TemplateRefImport(),
            'Template'       => new TemplateImport(),
            'Question'       => new QuestionImport(),
            'Question_NC'    => new QuestionNcImport(),
            'Criteria'       => new CriteriaTableImport(),
            'Text_Box'        => new TextBoxImport(),
        ];
    }
}
