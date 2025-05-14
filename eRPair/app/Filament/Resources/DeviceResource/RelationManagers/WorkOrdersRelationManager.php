<?php

namespace App\Filament\Resources\DeviceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Group;
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

                    /**
                     * @todo POSIBLE CAGADA.
                     */
                    Forms\Components\TextInput::make('work_order_number_warranty')
                        ->label('Order Number')
                        ->hidden()
                        ->default(function(){
                            return null;
                            /**
                             * @todo logica para cuando clickes garantía
                             */
                        }),
                    Forms\Components\TextInput::make('work_order_number')
                        ->label('Order Number')     
                        ->hidden()
                        ->dehydrated(false), //no lo manda a la bbdd, se encarga el modelo
                    Forms\Components\Toggle::make('is_warranty')
                        ->label('Is Warranty')
                        ->hidden()
                        ->default(function () {
                            if (true) {
                                return false;

                                /**
                                 * @todo logica para activar is warranty el tocar boton de garantía de un boton 
                                 */
                            } else {
                            }
                        }),
                    Forms\Components\Select::make('user_id')
                        ->label('User')
                        ->relationship('user', 'name')
                        ->required()
                        ->hidden()
                        ->default(auth()->user()->id),

                    Forms\Components\Select::make('device_id')
                        ->label('Device')
                        ->default(function(){
                            return $this->getRelationship()->getParent()->id;
                            /**
                             * @todo Logica para obtener del padre.
                             */
                        }),

                    Forms\Components\Select::make('store_id')
                        ->label('Store')
                        ->relationship('store', 'name')
                        ->hidden()
                        ->default(fn() => session()->get('store_id')),

                    Forms\Components\Select::make('closure_id')
                        ->label('Closure')
                        ->relationship('closure', 'id')
                        ->default(null)
                        ->hidden(),
                    Forms\Components\Select::make('status_id')
                        ->label('Status')
                        ->relationship('status', 'name')
                        ->required()
                        ->hidden(),
                ]),

                Forms\Components\TextInput::make('failure')
                    ->label('Failure')
                    ->required(),
                Forms\Components\Textarea::make('private_comment')
                    ->label('Private Comment')
                    ->nullable(),
                Forms\Components\Textarea::make('comment')
                    ->label('Comment')
                    ->nullable(),
                Forms\Components\TextInput::make('physical_condition')
                    ->label('Physical Condition')
                    ->required(),
                Forms\Components\TextInput::make('humidity')
                    ->label('Humidity')
                    ->required(),
                Forms\Components\TextInput::make('test')
                    ->label('Test')
                    ->required(),
                Forms\Components\Select::make('repair_time_id')
                    ->label('Repair Time')
                    ->relationship('repairTime', 'name')
                    ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('work_order_number')
            ->columns([
                Tables\Columns\TextColumn::make('work_order_number'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
