<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Helpers\PermissionHelper;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreRelationManager extends RelationManager
{
    protected static string $relationship = 'stores';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('addres')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('work_order_number')
                    ->numeric()
                    ->dehydrated(false)
                ]);

    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('address'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
                AttachAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
            ])
            ->actions([
                EditAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
                DeleteAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
                DetachAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(PermissionHelper::isNotAdmin()),
                ]),
            ]);
    }
}
