<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Filament\Resources\DeviceResource\Pages\CreateDevice;
use Closure;
use constants;
use Filament\Actions\EditAction;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceRelationManager extends RelationManager
{
    protected static string $relationship = 'devices';

    public function form(Form $form): Form
    {
        /**
         * Formulario para creacion de dispositivo, con condiciones para determinados campos en funcion de lo que esté relleno o activo.
         */
        return $form

            ->schema([
                Forms\Components\Toggle::make('has_no_serial_or_imei')
                    ->label('No Serial or IMEI')
                    ->default(false)
                    ->reactive(),
                Forms\Components\TextInput::make('serial_number')
                    ->label(constants::SERIAL_NUMBER)
                    ->reactive()
                    ->required(fn(Get $get) => !$get('IMEI') && !$get('has_no_serial_or_imei'))
                    ->disabled(fn(Get $get) => $get('has_no_serial_or_imei'))
                    ->minLength(6)
                    ->dehydrated(fn(Get $get) => !$get('has_no_serial_or_imei')),
                Forms\Components\TextInput::make('IMEI')
                    ->label(constants::IMEI)
                    ->reactive()
                    ->required(fn(Get $get) => !$get('serial_number') && !$get('has_no_serial_or_imei'))
                    ->disabled(fn(Get $get) => $get('has_no_serial_or_imei'))
                    ->minLength(15)
                    ->maxLength(15)
                    ->dehydrated(fn(Get $get) => !$get('has_no_serial_or_imei')),
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
                    ->afterStateUpdated(fn(callable $set) => $set('device_model_id', null))
                    ->required()
                    ->placeholder(function () {
                        if ($this->device->mode->id) {
                            return $this->device->mode->id;
                        }
                    }),

                Forms\Components\Select::make('device_model_id')
                    ->label('Modelo')
                    ->options(function (callable $get) {
                        $brandId = $get('brand_id');
                        if (!$brandId)
                            return [];

                        return \App\Models\DeviceModel::where('brand_id', $brandId)->pluck('name', 'id');
                    })
                    ->required(),
                Forms\Components\Select::make('client_id')
                    ->default(fn($livewire) => $livewire->ownerRecord->id)
                    ->hidden()
                    ->disabled(),
            ])
            ->action([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('model.brand.name')
                    ->label('Marca'),
                Tables\Columns\TextColumn::make('model.name')
                    ->label(constants::MODELO),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label(constants::SERIAL_NUMBER),
                Tables\Columns\TextColumn::make('IMEI')
                    ->label(constants::IMEI),
                Tables\Columns\TextColumn::make('colour')
                    ->label(constants::COLOUR),
                Tables\Columns\TextColumn::make('unlock_code')
                    ->label(constants::UNLOCK_CODE)
                    ->toggleable(),
            ])
            ->recordUrl(fn($record) => url("/dashboard/devices/{$record->id}/edit"))
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => url("/dashboard/devices/{$record->id}/edit"))
                    ->openUrlInNewTab(false),
            ]);
    }
}
