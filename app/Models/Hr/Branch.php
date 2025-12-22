<?php

namespace App\Models\Hr;

use App\Models\Hr\Recruitment\Candidate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
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

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }
}
