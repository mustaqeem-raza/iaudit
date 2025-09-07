<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryIaudit extends Model
{
    protected $table = 'categories_iaudit';
    protected $primaryKey = 'category_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function questions()
    {
        return $this->hasMany(QuestionIaudit::class, 'category_id', 'category_id');
    }
}