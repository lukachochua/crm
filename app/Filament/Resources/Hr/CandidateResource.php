<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\RecruitmentStage;
use App\Filament\Resources\Hr\CandidateResource\Pages;
use App\Models\Hr\Recruitment\Candidate;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'HR';

    protected static ?int $navigationSort = 60;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->maxLength(255)
                    ->nullable(),
                Select::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('stage')
                    ->options(static::stageOptions())
                    ->required(),
                DateTimePicker::make('applied_at')
                    ->nullable(),
                TextInput::make('source')
                    ->maxLength(255)
                    ->nullable(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('last_name')
                    ->label('Candidate')
                    ->formatStateUsing(fn (Candidate $record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('position.name')
                    ->label('Position')
                    ->sortable(),
                TextColumn::make('stage')
                    ->badge()
                    ->formatStateUsing(fn (RecruitmentStage|string $state): string => ($state instanceof RecruitmentStage ? $state : RecruitmentStage::from($state))->label())
                    ->color(fn (RecruitmentStage|string $state): string => ($state instanceof RecruitmentStage ? $state : RecruitmentStage::from($state))->color())
                    ->sortable(),
                TextColumn::make('applied_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Candidate $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (Candidate $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new Candidate())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'view' => Pages\ViewCandidate::route('/{record}'),
            'edit' => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }

    protected static function stageOptions(): array
    {
        $options = [];

        foreach (RecruitmentStage::cases() as $stage) {
            $options[$stage->value] = $stage->label();
        }

        return $options;
    }
}
