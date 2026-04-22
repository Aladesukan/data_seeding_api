<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Profile extends Model
{
    
    protected $table = 'profiles';
    public $timestamps = false;

    protected $hidden = ['updated_at'];
    protected $fillable = [
        'id',
        'name', 
        'gender',
        'gender_probability',
        'age',
        'age_group',
        'country_id',
        'country_name',
        'country_probability',      
    ];

    protected static function boot()
    { 
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    
}
