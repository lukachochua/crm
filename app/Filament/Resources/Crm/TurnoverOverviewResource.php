<?php

namespace App\Filament\Resources\Crm;

use App\Filament\Exports\Crm\TurnoverOverviewExporter;
use App\Filament\Resources\Crm\TurnoverOverviewResource\Pages;
use App\Models\Crm\TurnoverOverview;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class TurnoverOverviewResource extends Resource
{
    protected static ?string $model = TurnoverOverview::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Turnover';

    protected static ?int $navigationSort = 8;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period')
                    ->label('Period')
                    ->sortable(),
                TextColumn::make('total_invoiced')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('total_paid')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('outstanding_amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
            ])
            ->filters([
                Filter::make('period')
                    ->form([
                        TextInput::make('period')
                            ->placeholder('YYYY-MM'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['period'] ?? null,
                            fn (Builder $query, string $period): Builder => $query->where('period', 'like', $period . '%')
                        );
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(TurnoverOverviewExporter::class)
                    ->visible(fn (): bool => Gate::allows('export', TurnoverOverview::class)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTurnoverOverviews::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
