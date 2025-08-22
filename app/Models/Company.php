<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'cruise_companies';
    // protected $fillable = ['name', 'logo', 'css', 'mnemonic'];
    // public $timestamps = true;

    public function fleets()
    {
        return $this->hasMany(Fleet::class, 'cruise_company_id', 'id');
    }
}
