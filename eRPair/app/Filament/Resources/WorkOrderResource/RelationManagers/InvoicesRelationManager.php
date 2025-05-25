<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use app\Helpers\PermissionHelper;
use App\Models\Invoice;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'id')
                    ->required()
                    ->default(auth()->user()->id),
                Forms\Components\Select::make('store_id')
                    ->relationship('store', 'id')
                    ->default(auth()->user()->id)
                    ->required(),
                Forms\Components\TextInput::make('work_order_number')
                    ->numeric()
                    ->nullable(),
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'id')
                    ->nullable(),
                Section::make([
                    Forms\Components\Toggle::make('is_down_payment')
                        ->label('Pago anticipado')
                        ->default(false),
                    Forms\Components\TextInput::make('down_payment_value')
                        ->label('Cantidad pagada')
                        ->numeric()
                        ->disabled(fn($get) => $get('is_down_payment')),
                ])
                    ->label("Pago anticipado"),

                Forms\Components\TextInput::make('full_amount')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('taxes_full_amount')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('down_payment_amount')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('comment')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\Select::make('work_order_id')
                    ->relationship('workOrder', 'id')
                    ->nullable(),
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'id')
                    ->nullable(),
                Forms\Components\Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->default(1)
                    ->required(),
            ])
            ->disabled(function($record) { return PermissionHelper::NotAvailableOutsideStore($record);});
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number'),
            ])
            ->filters([

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
