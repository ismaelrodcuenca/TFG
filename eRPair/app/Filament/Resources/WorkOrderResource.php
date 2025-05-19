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
use App\Helpers\PermissionHelper;
use App\Models\Device;
use App\Models\Store;
use App\Models\WorkOrder;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Mail\Mailables\Content;
use PhpParser\Node\Stmt\Label;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';


    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isTechnicion();
    }

    public static ?string $navigationGroup = 'Taller';
    protected static ?string $label = 'Hojas de pedido';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make("Dispositivo")
                            ->schema([

                                Placeholder::make("")
                                    ->content(function (callable $get) {
                                        $deviceId = $get('device_id');

                                        if (!$deviceId) {
                                            return 'Sin dispositivo';
                                        }

                                        $device = Device::with('model.brand')->find($deviceId);

                                        if (!$device) {
                                            return 'Dispositivo no encontrado';
                                        }

                                        $modelo = $device->model->name ?? 'Modelo desconocido';
                                        $marca = $device->model->brand->name ?? 'Marca desconocida';

                                        return "{$marca} {$modelo}";
                                    }),

                            ])
                            ->icon("heroicon-o-device-phone-mobile")
                            ->columnSpan(1),

                        Section::make("Tienda")
                            ->schema([

                                Placeholder::make("")
                                    ->content(function () {
                                        $store = Store::findOrFail(session('store_id'), ['name']);
                                        return $store['name'];
                                    }),

                            ])
                            ->icon('heroicon-o-building-storefront')
                            ->columnSpan(1),

                        Section::make("Creado Por")
                            ->schema([

                                Placeholder::make("")
                                    ->content(fn() => auth()->user()->name ?? ''),

                            ])
                            ->icon('heroicon-o-user-circle')
                            ->columnSpan(1)

                    ]),

                        Grid::make(4)
                            ->schema([
                                Section::make("Documento")
                                    ->schema([

                                        Placeholder::make("")
                                            ->content(fn(callable $get) => Device::find($get('device_id'))->client->document ?? 'Sin Cliente'),

                                    ])
                                    ->columnSpan(1),

                                Section::make("Nombre")
                                    ->schema([

                                        Placeholder::make("")
                                           ->content(function(callable $get){ 
                                            $name = Device::find($get('device_id'))->client->name ?? 'John';
                                            $surname = Device::find($get('device_id'))->client->surname ?? 'Doe';
                                            $surname2 = Device::find($get('device_id'))->client->surname2;
                                            if($surname2){
                                                return $name ." ". $surname ." ". $surname2;
                                            }
                                            return $name. " ". $surname;
                                        }),

                                    ])
                                    ->columnSpan(2),

                                
                                Section::make("Telefono")
                                    ->schema([

                                        Placeholder::make("")
                                            ->content(fn(callable $get) => Device::find($get('device_id'))->client->phone_number ?? '696 696 696'),

                                    ])
                                    ->columnSpan(1),

                            ]),

                Group::make()->schema([
                    Hidden::make('user_id')
                        ->default(auth()->user()->id)
                        ->dehydrated(true),
                    Hidden::make('store_id')
                        ->default(session('store_id'))
                        ->dehydrated(true),
                    Hidden::make('device_id')
                        ->default(function (callable $get) {
                            return $get('device_id') ?? '';
                        })
                        ->dehydrated(true),
                    Hidden::make('work_order_number_warranty')
                        ->dehydrated(true)
                        ->default(null),
                    Hidden::make('is_warranty')
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

    public static function table(Table $table): Table
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
    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            InvoicesRelationManager::class,
            StatusRelationManager::class,
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
