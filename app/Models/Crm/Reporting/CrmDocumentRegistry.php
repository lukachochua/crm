<?php

namespace App\Models\Crm\Reporting;

use App\Filament\Resources\Crm\ApplicationResource;
use App\Filament\Resources\Crm\InvoiceResource;
use App\Filament\Resources\Crm\OrderResource;
use App\Filament\Resources\Crm\PaymentResource;
use App\Filament\Resources\Crm\ReservationResource;
use Illuminate\Database\Eloquent\Model;

class CrmDocumentRegistry extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = 'document_key';

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function viewUrl(): ?string
    {
        return match ($this->document_type) {
            'application' => ApplicationResource::getUrl('view', ['record' => $this->document_id]),
            'order' => OrderResource::getUrl('view', ['record' => $this->document_id]),
            'reservation' => ReservationResource::getUrl('view', ['record' => $this->document_id]),
            'invoice' => InvoiceResource::getUrl('view', ['record' => $this->document_id]),
            'payment' => PaymentResource::getUrl('view', ['record' => $this->document_id]),
            default => null,
        };
    }
}
