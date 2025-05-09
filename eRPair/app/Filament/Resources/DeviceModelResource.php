<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceModelResource\Pages;
use App\Filament\Resources\DeviceModelResource\RelationManagers;
use App\Models\DeviceModel;
use constants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceModelResource extends Resource
{
    protected static ?string $model = DeviceModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Modelo';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(constants::NAME_TYPO)
                    ->required(),
                Forms\Components\Select::make('brand_id')
                    ->label(constants::MARCA)
                    ->relationship('brand', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(constants::NAME_TYPO)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make( 'brand.name')
                    ->label(constants::MARCA)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeviceModels::route('/'),
            'create' => Pages\CreateDeviceModel::route('/create'),
            'edit' => Pages\EditDeviceModel::route('/{record}/edit'),
        ];
    }
}
