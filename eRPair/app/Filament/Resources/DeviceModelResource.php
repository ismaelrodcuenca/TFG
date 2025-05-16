<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceModelResource\Pages;
use App\Filament\Resources\DeviceModelResource\RelationManagers;
use App\Filament\Resources\DeviceModelResource\RelationManagers\ItemsRelationManager;
use App\Helpers\PermissionHelper;
use App\Models\Brand;
use App\Models\DeviceModel;
use constants;
use DB;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceModelResource extends Resource
{
    protected static ?string $model = DeviceModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $label = 'Modelo';

    public static ?string $navigationGroup = 'Catálogo';

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

                Tables\Columns\TextColumn::make('brand.name')
                    ->label(constants::MARCA)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(constants::MODELO)
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('brand.name', 'asc')
            ->filters([
                SelectFilter::make('brand_id')
                    ->options(DB::table('brands')->orderBy('name', 'asc')->pluck('name', 'id')->toArray())
                    ->label('Marca')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => url("/dashboard/device-models/{$record->id}/edit"))
                    ->openUrlInNewTab(false)
                    ->hidden(PermissionHelper::isNotAdmin()),
            ]);
        ;
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
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
