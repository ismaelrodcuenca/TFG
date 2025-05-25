<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\StoreRelationManager;
use App\Helpers\PermissionHelper;
use App\Models\User;
use constants;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $label = 'Usuario';

    protected static ?string $navigationGroup = 'Recursos';

    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isAdmin();
    }

    public static function getEloquentQuery(): Builder
{
    return PermissionHelper::isNotAdmin() ? parent::getEloquentQuery()
        ->where('id', auth()->user()->id) : parent::getEloquentQuery();// o cualquier otra condición
}
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
                Action::make("Desactivar")
                    ->icon('heroicon-o-user-minus')
                    ->color('warning'),
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
