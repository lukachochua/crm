<?php

namespace App\Filament\Resources\Hr;

use App\Filament\Resources\Hr\EmployeeDocumentResource\Pages;
use App\Models\Hr\Employee;
use App\Models\Hr\EmployeeDocument;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class EmployeeDocumentResource extends HrResource
{
    protected static ?string $model = EmployeeDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $hrNavigationGroup = 'People';

    protected static ?int $navigationSort = 30;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Employee $record): string => $record->user?->name ?? ('Employee #' . $record->id))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('document_type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('file_path')
                    ->directory('hr/employee-documents')
                    ->storeFileNamesIn('file_name')
                    ->preserveFilenames()
                    ->required(),
                TextInput::make('mime_type')
                    ->maxLength(255)
                    ->required(),
                DatePicker::make('expires_on')
                    ->nullable(),
                Select::make('uploaded_by')
                    ->label('Uploaded By')
                    ->relationship('uploader', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('document_type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('file_name')
                    ->label('File')
                    ->searchable(),
                TextColumn::make('expires_on')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (EmployeeDocument $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (EmployeeDocument $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new EmployeeDocument())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeDocuments::route('/'),
            'create' => Pages\CreateEmployeeDocument::route('/create'),
            'view' => Pages\ViewEmployeeDocument::route('/{record}'),
            'edit' => Pages\EditEmployeeDocument::route('/{record}/edit'),
        ];
    }
}
