<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'notes',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
