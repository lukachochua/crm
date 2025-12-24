<?php

namespace App\Filament\Resources\Crm;

use App\Enums\Crm\InternalTransferStatus;
use App\Filament\Resources\Crm\InternalTransferResource\Pages;
use App\Models\Crm\Operations\InternalTransfer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InternalTransferResource extends Resource
{
    protected static ?string $model = InternalTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-circle';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
                TextInput::make('source_location')
                    ->required()
                    ->maxLength(255),
                TextInput::make('destination_location')
                    ->required()
                    ->maxLength(255),
                Select::make('requested_by')
                    ->relationship('requestedBy', 'name')
                    ->default(fn (): ?int => Auth::id())
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('requested_at')
                    ->required(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->default(InternalTransferStatus::Draft->value)
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('source_location')
                    ->label('Source')
                    ->searchable(),
                TextColumn::make('destination_location')
                    ->label('Destination')
                    ->searchable(),
                TextColumn::make('requestedBy.name')
                    ->label('Requested by')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (InternalTransferStatus|string $state): string => ($state instanceof InternalTransferStatus ? $state : InternalTransferStatus::from($state))->label())
                    ->color(fn (InternalTransferStatus|string $state): string => ($state instanceof InternalTransferStatus ? $state : InternalTransferStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(static::statusOptions()),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (InternalTransfer $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (InternalTransfer $record): bool => Gate::allows('delete', $record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternalTransfers::route('/'),
            'create' => Pages\CreateInternalTransfer::route('/create'),
            'view' => Pages\ViewInternalTransfer::route('/{record}'),
            'edit' => Pages\EditInternalTransfer::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (InternalTransferStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
