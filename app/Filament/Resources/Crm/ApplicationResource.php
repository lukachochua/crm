<?php

namespace App\Filament\Resources\Crm;

use App\Enums\Crm\ApplicationStatus;
use App\Filament\Exports\Crm\ApplicationExporter;
use App\Filament\Resources\Crm\ApplicationResource\Pages;
use App\Models\Crm\Sales\Application;
use App\Models\Crm\Parties\Customer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn (Customer $record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                DateTimePicker::make('requested_at')
                    ->required(),
                Select::make('source')
                    ->options([
                        'walk-in' => 'Walk-in',
                        'phone' => 'Phone',
                        'online' => 'Online',
                    ])
                    ->nullable(),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                Textarea::make('internal_notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn (Application $record): string => $record->customer->first_name . ' ' . $record->customer->last_name)
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ApplicationStatus|string $state): string => ($state instanceof ApplicationStatus ? $state : ApplicationStatus::from($state))->label())
                    ->color(fn (ApplicationStatus|string $state): string => ($state instanceof ApplicationStatus ? $state : ApplicationStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(static::statusOptions()),
                Filter::make('requested_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('requested_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('requested_at', '<=', $date)
                            );
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ApplicationExporter::class)
                    ->visible(fn (): bool => Gate::allows('export', Application::class)),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Application $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (Application $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new Application())),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'view' => Pages\ViewApplication::route('/{record}'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (ApplicationStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
