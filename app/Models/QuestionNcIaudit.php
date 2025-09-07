<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionNcIaudit extends Model
{
    protected $table = 'question_ncs_iaudit';
    public $timestamps = false;

    protected $fillable = [
        'question_id',
        'nc_heading', 'nc_text',
        'nc_rem_hd', 'nc_con_hd', 'nc_usph_hd', 'nc_ipm_hd',
        'nc_rem_text', 'nc_con_text', 'nc_usph_text', 'nc_ipm_ref',
    ];
    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(QuestionIaudit::class, 'question_id', 'question_id');
    }
}
