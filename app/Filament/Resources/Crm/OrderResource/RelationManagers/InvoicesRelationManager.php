<?php

namespace App\Filament\Resources\Crm\OrderResource\RelationManagers;

use App\Enums\InvoiceStatus;
use App\Models\Crm\Invoice;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Gate::allows('viewAny', Invoice::class);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('invoice_number')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->options($this->statusOptions())
                    ->required(),
                TextInput::make('total_amount')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('issued_at')
                    ->required(),
                DateTimePicker::make('due_date')
                    ->nullable(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (InvoiceStatus|string $state): string => ($state instanceof InvoiceStatus ? $state : InvoiceStatus::from($state))->label())
                    ->color(fn (InvoiceStatus|string $state): string => ($state instanceof InvoiceStatus ? $state : InvoiceStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options($this->statusOptions()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => Gate::allows('create', Invoice::class)),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Invoice $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (Invoice $record): bool => Gate::allows('delete', $record)),
            ]);
    }

    private function statusOptions(): array
    {
        $options = [];

        foreach (InvoiceStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
