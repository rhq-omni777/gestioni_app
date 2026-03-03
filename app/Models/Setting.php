<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'autoload',
    ];

    protected $casts = [
        'value' => 'array',
        'autoload' => 'boolean',
    ];
}
