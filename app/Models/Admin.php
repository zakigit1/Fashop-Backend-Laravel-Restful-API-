<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;


class Admin extends User
{
    use HasApiTokens;
    
    protected static function booted()
    {
        static::addGlobalScope('admin', function (Builder $builder) {
            $builder->where('role', 'admin');
        });
    }
}