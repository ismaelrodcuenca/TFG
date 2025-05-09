<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashDeskResource\Pages;
use App\Filament\Resources\CashDeskResource\RelationManagers;
use App\Models\CashDesk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CashDeskResource extends Resource
{
    protected static ?string $model = CashDesk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Cajas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cash_float')
                    ->label('Cash Float')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('cash_amount')
                    ->label('Cash Amount')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('card_amount')
                    ->label('Card Amount')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('measured_cash_amount')
                    ->label('CALCULARLO')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('measured_card_amount')
                    ->label('CALCULARLO')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('difference_in_cash_amount')
                    ->label('CALCULARLO')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('difference_in_card_amount')
                    ->label('CALCULARLO')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('CALCULARLO')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('store_id')
                    ->label('CALCULARLO')
                    ->relationship('store', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashDesks::route('/'),
            'create' => Pages\CreateCashDesk::route('/create'),
            'edit' => Pages\EditCashDesk::route('/{record}/edit'),
        ];
    }
}
