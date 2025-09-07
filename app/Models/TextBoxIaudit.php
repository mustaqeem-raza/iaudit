<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextBoxIaudit extends Model
{
    protected $table = 'text_boxes_iaudit';
    protected $primaryKey = 'text_box_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function reference()
    {
        return $this->belongsTo(TemplateRefIaudit::class, 'reference_id', 'reference_id');
    }
}
