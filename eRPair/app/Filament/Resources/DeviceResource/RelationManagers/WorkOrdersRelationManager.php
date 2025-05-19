<?php

namespace App\Filament\Resources\DeviceResource\RelationManagers;

use App\Models\Device;
use App\Models\Store;
use App\Models\WorkOrder;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'workOrders';


    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()->schema([
                    Forms\Components\Placeholder::make("Dispositivo")
                        ->content(function () {
                            $modelo = $this->getOwnerRecord()->model->name ?? 'ni modelo';
                            $marca = $this->getOwnerRecord()->model->brand->name ?? 'Sin marca';
                            return $marca . " " . $modelo;
                        }),

                    Placeholder::make("Tienda")
                        ->content(function () {
                            $store = Store::findOrFail(session('store_id'), ['name']);
                            return $store['name'];
                        }),
                    Hidden::make('user_id')
                        ->default(auth()->user()->id)
                        ->dehydrated(true),
                    Hidden::make('store_id')
                        ->default(session('store_id'))
                        ->dehydrated(true),
                    Hidden::make('device_id')
                        ->default(function () {
                            return $this->getOwnerRecord()->id;
                        })
                        ->dehydrated(true),
                    Hidden::make('work_order_number_warranty')
                        ->dehydrated(true)
                        ->default(null),
                    Toggle::make('is_warranty')
                        ->default(false)
                        ->dehydrated(true),
                ]),

                Forms\Components\Textarea::make('failure')
                    ->label('Failure')
                    ->default(fn($record) => $record?->failure)
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('private_comment')
                    ->label('Private Comment')
                    ->nullable()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('comment')
                    ->label('Comment')
                    ->nullable()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('physical_condition')
                    ->label('Physical Condition')
                    ->required()
                    ->columnSpan('full')
                    ->dehydrated(true),
                Forms\Components\Textarea::make('humidity')
                    ->label('Humidity')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('test')
                    ->label('Test')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Select::make('repair_time_id')
                    ->label('Repair Time')
                    ->relationship('repairTime', 'name')
                    ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default'),
                Tables\Columns\TextColumn::make('work_order_number')
                    ->label('Work Order Number')
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default'),
                Tables\Columns\IconColumn::make('is_warranty')
                    ->label('Warranty')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                ->label('Último Status')
                ->getStateUsing(function ($record) {
                    return $record->statuses()->orderBy('created_at', 'desc')->first()->name ?? '-';
                })
                ->color(fn($record) => $record->is_warranty ? 'success' : 'default'),
                Tables\Columns\TextColumn::make('closure.name')
                    ->label('Closure')
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default'),
                Tables\Columns\TextColumn::make('repairTime.name')
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default')
                    ->label('Repair Time'),
            ])
            ->recordTitleAttribute('work_order_number')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->hidden(
                    fn($record) => now()->diffInMinutes($record->created_at) > 1
                ),
            ]);
    }
}
