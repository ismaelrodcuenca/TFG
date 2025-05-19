<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers\DeviceModelsRelationManager;
use App\Filament\Resources\ItemResource\RelationManagers\StoresRelationManager;
use App\Helpers\PermissionHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Type;
use constants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-battery-0';
    public static ?string $navigationGroup = 'Catálogo';
        public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isSalesperson();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(constants::NAME_TYPO),
                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->required()
                    ->label(constants::COST),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->label(constants::PRICE),
                Forms\Components\TextInput::make('distributor')
                    ->required()
                    ->label(constants::DISTRIBUTOR),
                Forms\Components\Select::make('type_id')
                    ->relationship('type', 'name')
                    ->label(constants::TYPE),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label(constants::CATEGORY),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(constants::NAME_TYPO)
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(constants::PRICE)
                    ->sortable()
                    ->numeric()
                    ->alignment(Alignment::Center)
                    ->suffix('€')
                    ->toggleable(true, false),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Coste')
                    ->sortable()
                    ->suffix('€')
                    ->alignment(Alignment::Center)
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('distributor')
                    ->label(constants::DISTRIBUTOR)
                    ->searchable()
                    ->alignment(Alignment::Center)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label(constants::TYPE)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(constants::CATEGORY)
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Categorias')
                    ->options(Category::all()->pluck('name', 'id')->toArray())
                    ->default(3),
                SelectFilter::make('type_id')
                    ->label('Tipo')
                    ->options(Type::all()->pluck('name', 'id')->toArray())
                    ->default(8),
                SelectFilter::make('deviceModels')
                    ->label('Modelos')
                    ->options(
                        DeviceModel::all()->pluck('name', 'id')->toArray()
                    ),
                Filter::make('name'),
                Filter::make('price'),
                Filter::make('costo')
                    ->default(),
                Filter::make('distributor'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        //Permite filtrar el array de nulos
        return array_filter([
            DeviceModelsRelationManager::class,
            StoresRelationManager::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
