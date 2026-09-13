<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'city_id',
        'name',
        'code',
        'slug',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function users(){
        return $this->belongsToMany(User::class,'user_branches');
    }
}
