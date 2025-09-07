<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubHeadingIaudit extends Model
{
    protected $table = 'sub_headings_iaudit';
    protected $primaryKey = 'subheading_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function heading()
    {
        return $this->belongsTo(HeadingIaudit::class, 'heading_id', 'heading_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionIaudit::class, 'subheading_id', 'subheading_id');
    }
}
