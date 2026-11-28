<?php

namespace App\Http\Controllers;

use App\Models\QuestionIaudit;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * The database management Overview page: the mockup's first screen, with its
 * "things to try" box replaced by live numbers from the question bank and
 * each capability marked done or coming, from config/database-management.php.
 */
class OverviewController extends Controller
{
    public function index(): View
    {
        $total = QuestionIaudit::questionRows()->count();
        $active = QuestionIaudit::questionRows()->active()->count();
        $lastImport = QuestionIaudit::questionRows()->max('imported_at');

        return view('overview', [
            'stats' => [
                'Questions'            => $total,
                'Active'               => $active,
                'Retired'              => $total - $active,
                'Added on screen'      => QuestionIaudit::questionRows()
                    ->where('short_code', 'like', QuestionIaudit::SCREEN_CODE_PREFIX . '%')
                    ->count(),
                'Templates'            => QuestionIaudit::templateCodes()->implode(', '),
                'Last workbook import' => $lastImport ? Carbon::parse($lastImport)->format('d M Y, H:i') : 'Never',
            ],
            'capabilities' => config('database-management.capabilities'),
            'weeksDone'    => config('database-management.weeks_done'),
        ]);
    }
}
