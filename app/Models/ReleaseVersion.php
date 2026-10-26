<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleaseVersion extends Model
{
    use HasFactory;

    protected $fillable = ['version', 'notes', 'released_at'];

    protected $casts = ['released_at' => 'datetime'];
}
