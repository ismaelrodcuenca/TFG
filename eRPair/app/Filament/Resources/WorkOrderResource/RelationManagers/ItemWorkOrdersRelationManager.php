<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use App\Filament\Resources\ItemResource;
use app\Helpers\PermissionHelper;
use App\Models\Category;
use App\Models\Item;
use App\Models\Type;
use constants;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;

class ItemWorkOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'itemWorkOrders';

    protected static ?string $title = 'Items';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Group::make([
                Toggle::make('only_model_items')
                    ->label('Mostrar solo items del modelo')
                    ->default(true)
                    ->reactive()
                    ->columnSpan('full')
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        if ($state) {
                            $set('item_id', null);
                        }
                    }),
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->searchable(fn($get) => $get('only_model_items') ? false : true)
                    ->options(function ($get) {
                        $parent = $this->getOwnerRecord();
                        if ($get('only_model_items') && $parent->device && $parent->device->model->id) {
                            return Item::whereHas('deviceModels', function ($query) use ($parent) {
                                $query->where('device_model_id', $parent->device->model->id);
                            })->pluck('name', 'id');
                        }
                    })
                    ->columnSpan('full')
                    ->required(),
                TextInput::make('modified_amount')
                    ->label('Precio Modificado')
                    ->numeric()
                    ->required()
                    ->columnSpan('full'),
            ])
            ->label('Añadir Item')
            ->columns(1),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')->label('Titulo'),
                TextColumn::make('modified_amount')->label('Precio')
                    ->state(fn($record) => $record->modified_amount ?? $record->item->price)
                    ->color(fn($record) => $record->modified_amount ? 'warning' : 'black')
                    ->money("EUR"),
            ])
            ->headerActions([
                Action::make('crearNuevoItem')
                    ->label('Crear Item')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        TextInput::make('name')->label('Nombre')->required(),
                        TextInput::make('price')->label('Precio')->numeric()->required(),
                        TextInput::make('cost')->label('Coste')->numeric(),
                        TextInput::make('distributor')->label('Distribuidor'),
                        Select::make('type_id')
                            ->label('Tipo')
                            ->relationship('type', 'name') 
                            ->options(Type::all()->pluck('name', 'id')) 
                            ->required(),
                        Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'name')
                            ->options(Category::all()->pluck('name','id'))
                            ->required(),
                        Toggle::make('link_item_device_model')
                            ->label('Agregar Item al Modelo del Dispositivo')
                            ->default(true),
                    ])
                    ->action(function (array $data, $livewire) {
                        $item = Item::create([
                            'name' => $data['name'],
                            'price' => $data['price'],
                            'cost' => $data['cost'] ?? null,
                            'distributor' => $data['distributor'] ?? null,
                            'type_id' => $data['type_id'],
                            'category_id' => $data['category_id'],
                        ]);
                        if (!empty($data['link_item_device_model'])) {
                            $parent = $livewire->getOwnerRecord();
                            $device = $parent->device ?? null;
                            if ($device && $device->model->id) {
                                $item->deviceModels()->attach($device->model->id);
                            }
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('Item creado')
                            ->body('El nuevo item ha sido creado correctamente.')
                            ->success()
                            ->send();
                    })

                ,
                Tables\Actions\CreateAction::make()
                    ->label("Añadir Item")
                    ->icon('heroicon-o-plus')
                    ->visible(function(){
                        return PermissionHelper::optionsAvailableOnWorkOrder($this->getOwnerRecord());
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make()

                    ->label("Modificar")
                    ->disabled(fn($record) => PermissionHelper::optionsAvailableOnWorkOrder($this->getOwnerRecord()))
                    ->icon('heroicon-o-currency-euro')
                    ->color('warning')
                    ->form([
                        TextInput::make('modified_amount')
                            ->numeric()
                            ->required()
                            ->default(fn($record) => $record->pivot->modified_amount)
                            ->afterStateUpdated(function ($state, $record) {
                                $record->update(['modified_amount' => $state]);
                            }),
                    ]),
                Tables\Actions\DeleteAction::make()
                    ->label("Quitar")
                    ->icon('heroicon-o-trash')
                    ->disabled(fn($record) => PermissionHelper::optionsAvailableOnWorkOrder($this->getOwnerRecord())),
            ]);
    }
}
