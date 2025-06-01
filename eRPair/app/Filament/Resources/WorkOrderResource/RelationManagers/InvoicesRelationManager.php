<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use app\Helpers\PermissionHelper;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Facturas';
    public function form(Form $form): Form
    {
        return $form
           ->schema([
                \Filament\Forms\Components\Grid::make(2)
                    ->label("Introducir Importe")
                    ->schema([
                        \Filament\Forms\Components\Select::make('payment_method_id')
                            ->label('Método de pago')
                            ->options(\App\Models\PaymentMethod::all()->pluck('name', 'id'))
                            ->default(1)
                            ->searchable()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('full_amount')
                            ->label('Importe a cobrar')
                            ->numeric()
                            ->reactive()
                            ->minValue(0)
                            ->default(fn($record) => \App\Http\Controllers\InvoiceController::calcularTotal($record::getOwnerRecord()->id))
                            ->suffix('€'),
                    ]),
                \Filament\Forms\Components\Card::make('Importes')
                    ->columns(4)
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('Total Pagado:')
                            ->content(fn($record) => \App\Http\Controllers\InvoiceController::calcularTotal($record->id) . ' €')
                            ->columnSpan(1),
                        \Filament\Forms\Components\Placeholder::make('Total Pendiente')
                            ->content(fn($record) => \App\Http\Controllers\InvoiceController::calcularPendiente($record->id) . ' €')
                            ->columnSpan(1),
                        \Filament\Forms\Components\Placeholder::make('Base imponible')
                            ->content(fn($record) => \App\Http\Controllers\InvoiceController::calcularBase($record->id) . ' €')
                            ->columnSpan(1),
                        \Filament\Forms\Components\Placeholder::make('Impuestos')
                            ->content(fn($record) => \App\Http\Controllers\InvoiceController::calcularImpuestos($record->id) . ' €')
                            ->columnSpan(1),
                    ]),
                Section::make("Opciones")->schema([
                    \Filament\Forms\Components\Grid::make(2)->schema([
                        \Filament\Forms\Components\Select::make('company_id')
                            ->label('Buscar Empresa')
                            ->searchable(['cif', 'name', 'corporate_name'])
                            ->createOptionForm([
                                \Filament\Forms\Components\TextInput::make('cif')
                                    ->label('CIF')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->label('Name')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('corporate_name')
                                    ->label('Corporate Name')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('address')
                                    ->label('Address')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('postal_code')
                                    ->label('Postal Code')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('locality')
                                    ->label('Locality')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('province')
                                    ->label('Province')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('discount')
                                    ->label('Discount')
                                    ->numeric()->minValue(0)->maxValue(100)
                                    ->default(0)->suffix('%'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return \App\Models\Company::create($data);
                            })
                            ->relationship(
                                'company',
                                'name'
                            )
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->cif . ' - ' . $record->name)
                            ->columnSpan(1)
                            ->placeholder("Buscar"),
                        \Filament\Forms\Components\Toggle::make('is_down_payment')
                            ->label('¿Es pago anticipo?')
                            ->default(false)
                            ->columnSpan(1),
                    ])
                ]),
                \Filament\Forms\Components\TextInput::make('comment')
                    ->label('Comentario')
                    ->placeholder('Comentario sobre el cobro si aplica')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Importe')
                    ->money('eur', true),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Método de pago'),
                Tables\Columns\IconColumn::make('company.name')
                    ->label('Empresa'),
                Tables\Columns\IconColumn::make('is_down_payment')
                    ->label('Anticipo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\Action::make("Devolución")
                    ->label('Devolución')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        
                    })
                    ->visible(fn() => PermissionHelper::isWorkOrderInvoiced($this->getOwnerRecord())),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
