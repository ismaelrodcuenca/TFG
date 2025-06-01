<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\Invoice;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $label = 'Facturas';

    public static ?string $navigationGroup = 'Recursos';
    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::hasRole();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Repeater::make('items')
                    ->label('Productos')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del producto')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('Precio')
                            ->suffix('€')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ])
                    ->createItemButtonLabel('Agregar producto')
                    ->columns(3)
                    ->required(),
                    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_down_payment')
                    ->label('Anticipo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_amount')
                    ->label('Importe a cobrar')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method.name')
                    ->label('Método de pago')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Invoice $record): string => route('dashboard', ['record' => $record->id])),
            ])
            ->query(function () {
                if (PermissionHelper::isAdmin()) {
                    return Invoice::query()->where('work_order_Id', null);
                };
                return Invoice::query()
                    ->where('store_id', session('store_id'))->where('work_order_Id', null);
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
