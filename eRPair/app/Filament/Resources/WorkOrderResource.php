<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderResource\Pages;
use App\Filament\Resources\WorkOrderResource\RelationManagers;
use App\Filament\Resources\WorkOrderResource\RelationManagers\ClosureRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\DeviceRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\InvoicesRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\RepairTimeRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\StatusRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\UserRelationManager;
use App\Models\WorkOrder;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $label = 'Hojas de pedidos';

    //TEMPORALACTIVO protected static bool $shouldRegisterNavigation = false;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([

                    /**
                     * @category POSIBLE CAGADA.
                     */
                    Forms\Components\TextInput::make('work_order_number')
                        ->label('Order Number')
                        ->numeric()
                        ->disabled()
                        ->default(function () {
                            
                        }),
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
                                return true;
                            }
                        }),
                    Forms\Components\Select::make('user_id')
                        ->label('User')
                        ->relationship('user', 'name')
                        ->hidden()
                        ->default(auth()->user()->id),

                    Forms\Components\Select::make('device_id')
                        ->label('Device')
                        ->disabled()
                        ->default(function(){
                            
                        }),

                    Forms\Components\Select::make('store_id')
                        ->label('Store')
                        ->relationship('store', 'name')
                        ->required()
                        ->hidden()
                        ->default(fn() => session()->get('store_id')),

                    Forms\Components\Select::make('closure_id')
                        ->label('Closure')
                        ->relationship('closure', 'name')
                        ->nullable()
                        ->hidden(),
                    Forms\Components\Select::make('status_id')
                        ->label('Status')

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('work_order_number')
                    ->label('Order Number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('device.name')
                    ->label('Device')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Store')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('failure')
                    ->label('Failure')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
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
            DeviceRelationManager::class,
            ItemsRelationManager::class,
            InvoicesRelationManager::class,
            RepairTimeRelationManager::class,
            StatusRelationManager::class,
            UserRelationManager::class,
            ClosureRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
