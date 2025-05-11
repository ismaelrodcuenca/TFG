<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Filament\Resources\DeviceResource\RelationManagers;
use App\Filament\Resources\DeviceResource\RelationManagers\ItemsRelationManager;
use App\Models\Device;
use DragonCode\Support\Facades\Helpers\Boolean;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use constants;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $label = 'Dispositivo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('has_no_serial_or_imei')
                    ->label('No Serial or IMEI')
                    ->default(false),
                Forms\Components\TextInput::make('serial_number')
                    ->label(constants::SERIAL_NUMBER)
                    ->nullable(),
                Forms\Components\TextInput::make('IMEI')
                    ->label(constants::IMEI)
                    ->nullable(),
                Forms\Components\TextInput::make('colour')
                    ->label(constants::COLOUR)
                    ->required(),
                Forms\Components\TextInput::make('unlock_code')
                    ->label(constants::UNLOCK_CODE)
                    ->nullable(),
                    Forms\Components\Select::make('brand_id')
                    ->label('Marca')
                    ->options(function () {
                        return \App\Models\Brand::all()->pluck('name', 'id');
                    })
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('device_model_id', null))
                    ->required(),
    
                Forms\Components\Select::make('device_model_id')
                    ->label('Modelo')
                    ->options(function (callable $get) {
                        $brandId = $get('brand_id');
                        if (!$brandId) return [];
    
                        return \App\Models\DeviceModel::where('brand_id', $brandId)->pluck('name', 'id');
                    })
                    ->default(function (callable $get) {
                        $brandId = $get('brand');
                        if (!$brandId) return [
                            ''=> "Seleccione una marca",
                        ];
                        
                        return \App\Models\DeviceModel::where('brand_id', $brandId)->pluck('name', 'id');
                    })
                    ->required(),
                Forms\Components\Select::make('client_id')
                    ->label(constants::CLIENT)
                    ->relationship('client', 'document')
                    ->required()
                    ->hidden(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('model.name')
                    ->label(constants::MODELO)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('model.brand.name')
                    ->label('Brand')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('has_no_serial_or_imei')
                    ->color(fn($state): string => $state ? 'success' : 'danger')
                    ->icon(fn($state): ?string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->label('No SN o IMEI')
                    ->toggleable()
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label(constants::SERIAL_NUMBER)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('IMEI')
                    ->label(constants::IMEI)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('colour')
                    ->label(constants::COLOUR)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('unlock_code')
                    ->label(constants::UNLOCK_CODE)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('client.document')
                    ->label(constants::CLIENT)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Created At')
                        ->sortable()
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}