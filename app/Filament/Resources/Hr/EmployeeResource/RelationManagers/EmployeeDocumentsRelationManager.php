<?php

namespace App\Filament\Resources\Hr\EmployeeResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('file_name')
                    ->label('File')
                    ->searchable(),
                TextColumn::make('expires_on')
                    ->date()
                    ->sortable(),
                TextColumn::make('uploader.name')
                    ->label('Uploaded By')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
