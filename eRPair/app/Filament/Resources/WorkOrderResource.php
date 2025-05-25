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
use Date;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Mail\Mailables\Content;
use PhpParser\Node\Stmt\Label;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';


    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
 public static function getGloballySearchableAttributes(): array
    {
        return [
            'work_order_number',
        ];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
                'work_order_number' => ($record->work_order_number . " " . $record->device->model->brand->name . " - " . $record->device->model->name),
        ];
    }
    
        protected static ?string $label = 'Hojas de pedido';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        //Sección para el dispositivo
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
                        //Seccion para la tienda
                        Section::make("Tienda")
                            ->schema([

                                Placeholder::make("")
                                    ->content(content: function () {
                                        $store = Store::findOrFail(session('store_id'), ['name']);
                                        return $store['name'];
                                    }),

                            ])
                            ->icon('heroicon-o-building-storefront')
                            ->columnSpan(1),
                        //Seccion para el creador del pedido
                        Section::make("Creado Por")
                            ->schema([

                                Placeholder::make("")
                                    ->content(fn() => auth()->user()->name ?? ''),

                            ])
                            ->icon('heroicon-o-user-circle')
                            ->columnSpan(1)
                    ]),
                //Sección para el cliente
                Section::make('Cliente')
                    ->icon('heroicon-s-user')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                // Sección para el documento del cliente
                                Section::make("Documento")
                                    ->schema([
                                        
                                        Placeholder::make("")
                                            ->content(fn(callable $get) => Device::find($get('device_id'))->client->document ?? 'Sin Cliente'),

                                    ])
                                    ->columnSpan(1),
                                // Sección para el nombre del cliente
                                Section::make("Nombre")
                                    ->schema([

                                        Placeholder::make("")
                                            ->content(function (callable $get) {
                                                $name = Device::find($get('device_id'))->client->name ?? 'John';
                                                $surname = Device::find($get('device_id'))->client->surname ?? 'Doe';
                                                $surname2 = Device::find($get('device_id'))->client->surname2;
                                                if ($surname2) {
                                                    return $name . " " . $surname . " " . $surname2;
                                                }
                                                return $name . " " . $surname;
                                            }),

                                    ])
                                    ->columnSpan(2),

                                // Sección para el teléfono del cliente
                                Section::make("Telefono")
                                    ->schema([

                                        Placeholder::make("")
                                            ->content(fn(callable $get) => Device::find($get('device_id'))->client->phone_number ?? '696 696 696'),

                                    ])
                                    ->columnSpan(1),

                            ]),
                    ]),
                //Seccion oculta con los IDs necesarios
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

                //Sección para los datos del pedido
                Forms\Components\Textarea::make('failure')
                    ->label('Failure')
                    ->default(fn($record) => $record?->failure)
                    ->required()
                    ->columnSpan('full')
                    ->disabled(fn($record) => $record->created_at
                        ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7
                        : true),
                Forms\Components\Textarea::make('private_comment')
                    ->label('Private Comment')
                    ->nullable()
                    ->columnSpan('full')
                    ->disabled(fn($record) => $record->created_at
                        ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7
                        : true),
                Forms\Components\Textarea::make('comment')
                    ->label('Comment')
                    ->nullable()
                    ->columnSpan('full')
                    ->disabled(fn($record) => $record->created_at
                        ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7
                        : true),
                Forms\Components\Textarea::make('physical_condition')
                    ->label('Physical Condition')
                    ->required()
                    ->columnSpan('full')
                    ->dehydrated(true)
                    ->disabled(fn($record) => $record->created_at
                        ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7
                        : true),
                Forms\Components\Textarea::make('humidity')
                    ->label('Humidity')
                    ->required()
                    ->columnSpan('full')
                    ->disabled(fn($record) => $record->created_at
                        ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7
                        : true),
                Forms\Components\Textarea::make('test')
                    ->label('Test')
                    ->required()
                    ->columnSpan('full')
                    ->disabled(fn($record) => $record->created_at
                        ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7
                        : true),
                Forms\Components\Select::make('repair_time_id')
                    ->label('Repair Time')
                    ->relationship('repairTime', 'name')
                    ->required()
                    ->disabled(fn($record) => $record->created_at
                        ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7
                        : true),
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
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_warranty')
                    ->label('Warranty')
                    ->boolean(),
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Tienda')
                    ->hidden(PermissionHelper::isNotAdmin())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Último Status')
                    ->getStateUsing(function ($record) {
                        return $record->statuses()->orderBy('created_at', 'desc')->first()->name ?? '-';
                    })
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default'),
                Tables\Columns\TextColumn::make('repairTime.name')
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default')
                    ->label('Repair Time'),
            ])
            ->recordTitleAttribute('work_order_number')
            ->filters([
                Filter::make('is_warranty')
                    ->label('Mostrar solo garantías')
                    ->query(fn(Builder $query): Builder => $query->where('is_warranty', true)),
                Filter::make('created_at')
                    ->label('Fecha de creación')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde')
                            ->placeholder('Fecha inicio')
                            ->default(now()->subDays(7)),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta')
                            ->placeholder('Fecha fin')
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['created_from'])) {
                            $query->where('created_at', '>=', Date::make($data['created_from'])->startOfDay());
                        }
                        if (isset($data['created_until'])) {
                            $query->where('created_at', '<=', Date::make($data['created_until'])->endOfDay());
                        }
                        return $query;
                    }),
                Filter::make('status')
                    ->label('Mostrar cancelados o completados')
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('statuses', function ($q) {
                            $q->where(function ($query) {
                                $query->where('name', 'like', '%cancelado%')
                                    ->orWhere('name', 'like', '%completado%');
                            });
                        });
                    }),

                SelectFilter::make('stores')
                    ->label('Tienda')
                    ->relationship('store', 'name')
                    ->options(auth()->user()->stores()->pluck('name', 'stores.id')->toArray())
                    ->hidden(PermissionHelper::isNotAdmin()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => $record->created_at
                        ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7
                        : true),
            ])
            ->query(function () {
                if (PermissionHelper::isNotAdmin()) {
                    return WorkOrder::query()
                        ->where('store_id', session('store_id'))->orderBy('created_at', 'desc');
                }

                return WorkOrder::query();

            });
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
