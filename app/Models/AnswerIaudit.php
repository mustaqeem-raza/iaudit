<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerIaudit extends Model
{
    protected $table = 'answers_iaudit';

    protected $fillable = [
        'user_id',
        'ship_id',
        'question_id',
        'answer',
        'note',
        'files',
    ];

    protected $casts = [
        'files' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(QuestionIaudit::class, 'question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ship()
    {
        return $this->belongsTo(Ship::class, 'ship_id');
    }
}