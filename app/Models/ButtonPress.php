<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ButtonPress extends Model
{
    protected $fillable = [
        'total',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
        ];
    }
}
