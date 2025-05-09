<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\StoreRelationManager;
use App\Models\User;
use constants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label(constants::NAME)->required()->columnSpan(1),
                TextInput::make('email')->label(constants::EMAIL)->required()->columnSpan(1),
                TextInput::make('password')->label(constants::PASSWORD)->required()->columnSpan(1)->password()->hiddenOn('view')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label(constants::NAME)
                ->sortable()
                ->searchable(),
                TextColumn::make('email')
                ->label(constants::EMAIL)
                ->sortable()
                ->searchable(),
            ])    
            ->filters([
                
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StoreRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
