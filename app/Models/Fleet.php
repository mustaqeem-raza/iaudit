<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    protected $table = 'fleets';

    public function company()
    {
        return $this->belongsTo(Company::class, 'cruise_company_id', 'id');
    }

    public function ships()
    {
        return $this->hasMany(Ship::class, 'fleet_id', 'id');
    }
}
