<?php

namespace App\Models\Hr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'document_type',
        'title',
        'file_path',
        'file_name',
        'mime_type',
        'expires_on',
        'uploaded_by',
        'notes',
    ];

    protected $casts = [
        'expires_on' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
