<?php

namespace App\Filament\Resources\WorkOrderResource\Pages;

use App\Filament\Resources\WorkOrderResource;
use App\Helpers\PermissionHelper;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\WorkOrderController;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Status;
use App\Models\StatusWorkOrder;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;
use SeekableIterator;

class EditWorkOrder extends EditRecord
{
    protected static string $resource = WorkOrderResource::class;
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->disabled(fn() => PermissionHelper::isWorkOrderEditable($this->record));
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Hoja Pedido')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->url(fn($record) => route('generateWorkOrder', ['id' => $record->id]))
                ->openUrlInNewTab(condition: true),
            Action::make('Garantía')
                ->icon('heroicon-o-plus-circle')
                ->color(Color::Amber)
                ->visible(fn() => PermissionHelper::canAddWarranty($this->record))
                ->openUrlInNewTab()
                ->form([

                ])
                ->action(
                    function (array $data, $record) {
                        // Crear un nuevo WorkOrder con los datos proporcionados
                        WorkOrder::create([
                            'work_order_number'        => null,
                            'is_warranty'              => true,
                            'work_order_number_warranty' => $record->work_order_number,
                            'failure'                  => $data['failure'] ?? null,
                            'private_comment'          => $data['private_comment'] ?? null,
                            'comment'                  => $data['comment'] ?? null,
                            'physical_condition'       => $data['physical_condition'] ?? null,
                            'humidity'                 => $data['humidity'] ?? null,
                            'test'                     => $data['test'] ?? null,
                            'user_id'                  =>  auth()->user()->id,
                            'device_id'                => $$record->device_id,
                            'repair_time_id'           => $data['repair_time_id'] ?? null,
                            'store_id'                 => session('store_id'),
                            'deliverer_id'             => null,
                            'closure_id'               => null,
                        ]);
                    }
                ),
            Action::make('cobro')
                ->label('Cobrar')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->visible(fn() => PermissionHelper::canBeBilled($this->record) || PermissionHelper::isChargeAvailable($this->record) || InvoiceController::isFullyPayed($this->record->id))
                ->modalHeading('Cobro')
                ->form(function ($record) {
                    return [
                        Grid::make(2)
                            ->label("Introducir Importe")
                            ->schema([
                                Select::make('payment_method_id')
                                    ->label('Método de pago')
                                    ->options(PaymentMethod::all()->pluck('name', 'id'))
                                    ->default(1)
                                    ->searchable()
                                    ->required(),
                                TextInput::make('full_amount')
                                    ->label('Importe a cobrar')
                                    ->numeric()
                                    ->reactive()
                                    ->minValue(1)
                                    ->maxValue(InvoiceController::calcularPendiente($record->id))
                                    ->default(fn($record) => InvoiceController::calcularPendiente($record->id))
                                    ->suffix('€'),
                            ]),
                        Card::make('Importes')
                            ->columns(4)
                            ->schema([
                                Placeholder::make('total')
                                    ->label('Total Pagado')
                                    ->content(fn($record) => InvoiceController::calcularTotal($record->id) . ' €')
                                    ->columnSpan(1),
                                Placeholder::make('Total Pendiente')
                                    ->content(fn($record) => InvoiceController::calcularPendiente($record->id) . ' €')
                                    ->columnSpan(1),
                                Placeholder::make('base')
                                    ->label('Base Imponible')
                                    ->content(fn($record) => InvoiceController::calcularBase($record->id) . ' €')
                                    ->columnSpan(1),
                                Placeholder::make('taxes')
                                    ->label('Impuestos')
                                    ->content(fn($record) => InvoiceController::calcularImpuestos($record->id) . ' €')
                                    ->columnSpan(1),
                            ]),
                        Section::make("Opciones")->schema([
                            Grid::make(2)->schema([
                                Select::make('company_id')
                                    ->label('Buscar Empresa')
                                    ->searchable(['cif', 'name', 'corporate_name'])
                                    ->createOptionUsing(function (array $data) {
                                        return Company::create($data);
                                    })
                                    ->options(
                                        Company::all()->mapWithKeys(fn($company) => [
                                            $company->id => $company->cif . ' - ' . $company->name
                                        ])
                                    )
                                    ->createOptionForm([
                                        TextInput::make('cif')
                                            ->label('CIF')
                                            ->required(),
                                        TextInput::make('name')
                                            ->label('Name')
                                            ->required(),
                                        TextInput::make('corporate_name')
                                            ->label('Corporate Name')
                                            ->required(),
                                        TextInput::make('address')
                                            ->label('Address')
                                            ->required(),
                                        TextInput::make('postal_code')
                                            ->label('Postal Code')
                                            ->required(),
                                        TextInput::make('locality')
                                            ->label('Locality')
                                            ->required(),
                                        TextInput::make('province')
                                            ->label('Province')
                                            ->required(),
                                        TextInput::make('discount')
                                            ->label('Discount')
                                            ->numeric()->minValue(0)->maxValue(100)
                                            ->default(0)->suffix('%'),

                                    ])
                                    ->columnSpan(1)
                                    ->placeholder("Buscar"),
                                Toggle::make('is_down_payment')
                                    ->label('¿Es pago anticipo?')
                                    ->default(false)
                                    ->columnSpan(1),
                            ])
                        ]),
                        TextInput::make('comment')
                            ->label('Comentario')
                            ->placeholder('Comentario sobre el cobro si aplica'),
                    ];
                })
                ->action(function (array $data, $record) {
                    Invoice::create([
                        'invoice_number' => null,
                        'base' => InvoiceController::calcularBase($record->id),
                        'taxes' => InvoiceController::calcularImpuestos($record->id),
                        'total' => $data['full_amount'],
                        'is_refund' => false,
                        'is_down_payment' => $data['is_down_payment'] ?? false,
                        'work_order_id' => $record->id,
                        'client_id' => $record->device->client->id,
                        'store_id' => $record->store_id,
                        'company_id' => $data['company_id'],
                        'payment_method_id' => $data['payment_method_id'],
                        'user_id' => auth()->user()->id,
                        'comment' => $data['comment'] ?? null,
                    ]);
                    if (InvoiceController::isFullyPayed($record->id)) {
                        StatusWorkOrder::create([
                            'status_id' => Status::where('name', 'FACTURADO')->first()->id,
                            'work_order_id' => $record->id,
                            'user_id' => auth()->user()->id,
                        ]);
                    }

                    Notification::make()
                        ->title('Cobro realizado correctamente.')
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
                }),

            Action::make('entregar')
                ->label('Entregar')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(PermissionHelper::canBeDelivered($this->record))
                ->requiresConfirmation()
                ->action(function ($record) {
                    StatusWorkOrder::create([
                        'status_id' => Status::where('name', 'ENTREGADO')->first()->id,
                        'work_order_id' => $record->id,
                        'user_id' => auth()->user()->id,
                    ]);
                    Notification::make()
                        ->title('Pedido entregado correctamente.')
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
                }),
            Action::make('Devolución')
                ->icon('heroicon-o-credit-card')
                ->color(fn() => PermissionHelper::canBeRefunded($this->record) ? Color::Stone : Color::Orange)
                ->requiresConfirmation()
                ->visible(fn() => PermissionHelper::canBeRefunded($this->record))
                ->openUrlInNewTab()
                ->form([
                        Select::make('Factura')
                            ->label('Seleccione la factura a devolver o todas para el importe total')
                            ->options(InvoiceController::getInvoicesForRefund($this->record->id)),
                        Textarea::make('comment')
                            ->label('Comentario')
                            ->placeholder('Comentario sobre la devolución si aplica')
                            ->default('Devolución de factura #' . $this->record->invoice_number),
                    ])
                ->action(function () {
                    InvoiceController::createRefundInvoice(
                        $data['id'] ?? $this->record->id,
                        [
                            'comment' => $data['comment'] ?? 'Devolución de factura #' . $this->record->invoice_number,
                            'base' => InvoiceController::calcularBase($this->record->id) * -1,
                            'taxes' => InvoiceController::calcularImpuestos($this->record->id) * -1,
                            'total' => InvoiceController::calcularTotal($this->record->id) * -1,
                        ]
                    );
                }),
            Action::make('Cancelar')
                ->icon('heroicon-o-x-circle')
                ->color(Color::Red)
                ->requiresConfirmation()
                ->visible(PermissionHelper::canBeCanceled($this->record))
                ->disabled(fn() => PermissionHelper::optionsAvailableOnWorkOrder($this->record))
                ->openUrlInNewTab()
                ->action(function () {
                    $workOrder = $this->record;
                    StatusWorkOrder::create([
                        'status_id' => Status::where('name', 'CANCELADO')->first()->id,
                        'work_order_id' => $workOrder->id,
                        'user_id' => auth()->user()->id,
                    ]);
                    Notification::make()
                        ->title('Pedido cancelado.')
                        ->success()
                        ->send();
                }),
            Action::make("Info")
                ->icon('heroicon-o-information-circle')
                ->color('gray')
                ->action(fn() => PermissionHelper::infoNotification($this->record))
        ];
    }
}
