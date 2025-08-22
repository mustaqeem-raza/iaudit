<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    protected $table = 'ships';

    public function fleet()
    {
        return $this->belongsTo(Fleet::class, 'fleet_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'cruise_company_id', 'id');
    }
}
