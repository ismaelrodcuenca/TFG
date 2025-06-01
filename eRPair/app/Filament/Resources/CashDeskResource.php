<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashDeskResource\Pages;
use App\Filament\Resources\CashDeskResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\CashDesk;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use SebastianBergmann\CodeCoverage\Util\Percentage;

class CashDeskResource extends Resource
{
    protected static ?string $model = CashDesk::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $label = 'Cajas';
    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isSalesperson();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        /**
                         * @category POSIBLE CAGADA
                         */
                        /**
                         * @todo Logica calculo
                         */
                        Forms\Components\TextInput::make('measured_cash_amount')
                            ->label('CALCULARLO')
                            ->numeric()
                            ->required(),
                        /**
                         * @todo Logica calculo
                         */
                        Forms\Components\TextInput::make('measured_card_amount')
                            ->label('CALCULARLO')
                            ->numeric()
                            ->required(),
                        /**
                         * @todo Logica calculo
                         */
                        Forms\Components\TextInput::make('difference_in_cash_amount')
                            ->label('CALCULARLO')
                            ->numeric()
                            ->required(),
                        /**
                         * @todo Logica calculo
                         */
                        Forms\Components\TextInput::make('difference_in_card_amount')
                            ->label('CALCULARLO')
                            ->numeric()
                            ->required(),
                        //USER LISTO
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(auth()->user()->id),
                        //STORE LISTA
                        Forms\Components\Select::make('store_id')
                            ->label('Store')
                            ->relationship('store', 'name')
                            ->required()
                            ->default(session()->get('store_id')),
                    ])
                    ->hidden(),
                Group::make()
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
                        
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Cierre')
                    ->dateTime('Y-m-d'),
                Tables\Columns\TextColumn::make('cash_float')
                    ->label('Fondo'),
                Tables\Columns\TextColumn::make('cash_amount')
                    ->label('Efectivo'),
                Tables\Columns\TextColumn::make('card_amount')
                    ->label('Tarjeta'),
                Tables\Columns\TextColumn::make('measured_cash_amount')
                    ->label('Ef. contado'),
                Tables\Columns\TextColumn::make('measured_card_amount')
                    ->label('Tar.contada'),
                Tables\Columns\TextColumn::make('difference_in_cash_amount')
                    ->label('Diff. efectivo'),
                Tables\Columns\TextColumn::make('difference_in_card_amount')
                    ->label('Diff. tarjeta'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Creado por'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->label('Created At')
                    ->default(now()->subDay()->format('Y-m-d')),
                Filter::make('store_id')
                    ->default(session()->get('store_id'))
                    ->hidden(),

            ])
            ->actions([
            ])
            ->query(function (Builder $query) {
                if (PermissionHelper::isAdmin()) {
                    return CashDesk::query();
                }
                return CashDesk::query()->where('store_id', session()->get('store_id'))
                ->latest('created_at')->first();
            })
            ->defaultSort('created_at', 'desc');
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
        ];
    }
}
