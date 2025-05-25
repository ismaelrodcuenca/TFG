<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use App\Filament\Resources\CategoryResource;
use app\Helpers\PermissionHelper;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Type;
use constants;
use DB;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static function getTotalPriceForCurrentWorkOrder($record): float
    {
        $items = DB::table('item_work_order')
            ->where('work_order_id', $record->id)
            ->get();

        $total = 0;
        foreach ($items as $item) {
            $itemRecord = Item::find($item->item_id);
            if ($itemRecord) {
                $actualPrice = $item->modified_amount ?? $itemRecord->price;
                $total += $actualPrice;
            }
        }

        return $total;
    }

    public function form(Form $form): Form
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
                Toggle::make('link_item_device_model')
                    ->label('VINCULAR A MODELO')
                    ->default(true)
                    ->reactive(),
            ])
            ->disabled(function($record) { return PermissionHelper::NotAvailableOutsideStore($record);});
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('item_work_order_id')
                    ->label('item_work_order_id')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(constants::NAME_TYPO)
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label(constants::PRICE)
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->pivot?->modified_amount ?? $record->price)
                    ->numeric()
                    ->alignment(Alignment::Center)
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.') . ' €')
                    ->color(fn($record) => $record->pivot?->modified_amount ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('distributor')
                    ->label(constants::DISTRIBUTOR)
                    ->searchable()
                    ->alignment(Alignment::Center)
                    ->toggleable(true, true),
                Tables\Columns\TextColumn::make('type.name')
                    ->label(constants::TYPE)
                    ->sortable()
                    ->toggleable(true, true),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(constants::CATEGORY)
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Categorias')
                    ->options(Category::all()->pluck('name', 'id')->toArray()),
                SelectFilter::make('type_id')
                    ->label('Tipo')
                    ->options(Type::all()->pluck('name', 'id')->toArray()),
                Filter::make('name'),
                Filter::make('price'),
                Filter::make('costo'),
                Filter::make('distributor'),
            ])
            ->description('Total: ' . self::getTotalPriceForCurrentWorkOrder($this->ownerRecord) . "€")
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label("Crear Item")
                    ->after(function (Item $record, array $data, $livewire) {
                        if (!empty($data['link_item_device_model'])) {
                            $workOrder = $livewire->getOwnerRecord();
                            $deviceModelId = $workOrder?->device?->model;
                            if ($deviceModelId) {
                                $record->deviceModels()->syncWithoutDetaching([$deviceModelId]);
                            }
                        }
                        if (!empty($data['link_item_device_model'])) {
                            $deviceModelId = $livewire->getOwnerRecord()?->device?->device_model_id;

                            if ($deviceModelId) {
                                $record->deviceModels()->syncWithoutDetaching([$deviceModelId]);
                            }
                        }
                    }),
                Tables\Actions\AttachAction::make("Agregar")
                    ->label("Agregar Item")
                    
                    ->form(fn(AttachAction $action) => [
                        Toggle::make("only_model_related")
                            ->label("Mostrar solo respuestos del modelo:")
                            ->reactive()
                            ->default(true),
                        Select::make('type_id')
                            ->relationship('type', 'name')
                            ->label(constants::TYPE),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label(constants::CATEGORY),
                        $action->getRecordSelect()
                            ->label('Repuesto')
                            ->searchable(fn(callable $get) => !$get('only_model_related'))
                            ->options(function (callable $get) {
                                if (!$get('only_model_related')) {
                                    return Item::pluck('name', 'id')->toArray();
                                }

                                $deviceModelId = $this->getOwnerRecord()?->device?->device_model_id;

                                return $deviceModelId
                                    ? Item::whereHas('deviceModels', fn($q) => $q->where('device_model_id', $deviceModelId))
                                        ->pluck('name', 'id')
                                        ->toArray()
                                    : [];
                            }),

                    ]),


            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make("modified_amount")
                    ->label("Modificar")
                    ->icon('heroicon-o-currency-euro')
                    ->color('warning')
                    ->form([
                        TextInput::make("items.modified_amount")
                            ->numeric()
                            ->required()
                            ->default(fn($record) => $record->pivot->modified_amount)
                            ->afterStateUpdated(function ($state, $record) {
                                DB::table('item_work_order')
                                    ->where('item_work_order_id', $record->pivot_item_work_order_id)
                                    ->update(['modified_amount' => $state]);
                            })
                    ]),
                DetachAction::make()
                    ->label("Quitar")
                    ->icon('heroicon-o-trash')
                    ->action(function ($record) {
                        dd("Eliminando item: " . $record);
                        DB::table('item_work_order')
                            ->where('item_work_order_id', $record->pivot_item_work_order_id)
                            ->delete();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make("Eliminar"),
                ]),
            ]);
    }
}
