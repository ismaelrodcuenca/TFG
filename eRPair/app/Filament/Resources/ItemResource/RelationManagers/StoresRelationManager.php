<?php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use App\Helpers\PermissionHelper;
use App\Http\Middleware\EnsureStoreSelected;
use App\Models\Store;
use DB;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoresRelationManager extends RelationManager
{
    protected static string $relationship = 'stores';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxLength(255)
                    ->disabled(PermissionHelper::isNotManager()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return PermissionHelper::isAdmin()
                    ? $query
                    : $query->where('stores.id', session('store_id'));
            })
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('quantity'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->hidden(PermissionHelper::isNotAdmin()),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make()->hidden(PermissionHelper::isNotManager()),
                AttachAction::make()->hidden(PermissionHelper::isNotManager()),
            ])            
            ->filters([
                SelectFilter::make('stores.id')
                ->options(
                    auth()->user()->stores->pluck('name', 'id')->toArray()
                )
                ->visible(PermissionHelper::isAdmin()),
            ]);
    }
}
