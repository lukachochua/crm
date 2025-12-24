<?php

namespace App\Filament\Resources\Crm;

use App\Filament\Resources\Crm\CrmDocumentRegistryResource\Pages;
use App\Models\Crm\Reporting\CrmDocumentRegistry;
use App\Services\Crm\DocumentRegistryQuery;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CrmDocumentRegistryResource extends Resource
{
    protected static ?string $model = CrmDocumentRegistry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Document Registry';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getEloquentQuery(): Builder
    {
        $service = app(DocumentRegistryQuery::class);
        $query = $service->forUser(Auth::user());

        return CrmDocumentRegistry::query()->fromSub($query, 'crm_documents');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? Str::headline($state) : 'Unknown'),
                TextColumn::make('reference')
                    ->searchable(),
                TextColumn::make('related_customer')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? Str::headline(str_replace('_', ' ', $state)) : 'Unknown'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_by_name')
                    ->label('Created by')
                    ->formatStateUsing(fn (?string $state, CrmDocumentRegistry $record): string => $state ?? (string) ($record->created_by ?? '-')),
            ])
            ->recordUrl(fn (CrmDocumentRegistry $record): ?string => $record->viewUrl())
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCrmDocumentRegistries::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
