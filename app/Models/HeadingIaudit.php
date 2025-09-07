<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeadingIaudit extends Model
{
    protected $table = 'headings_iaudit';
    protected $primaryKey = 'heading_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function subCategories()
    {
        return $this->hasMany(SubCategoryIaudit::class, 'heading_id', 'heading_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionIaudit::class, 'heading_id', 'heading_id');
    }
}