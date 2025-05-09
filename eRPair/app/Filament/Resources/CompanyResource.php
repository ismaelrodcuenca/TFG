<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Empresas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cif')
                    ->label('CIF')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required(),
                Forms\Components\TextInput::make('corporate_name')
                    ->label('Corporate Name')
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->label('Address')
                    ->required(),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Postal Code')
                    ->required(),
                Forms\Components\TextInput::make('locality')
                    ->label('Locality')
                    ->required(),
                Forms\Components\TextInput::make('province')
                    ->label('Province')
                    ->required(),
                Forms\Components\TextInput::make('discount')
                    ->label('Discount')
                    ->numeric()->minValue(0)->maxValue(100)
                    ->default(0)->suffix('%'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('cif')
                ->label('CIF'),
            Tables\Columns\TextColumn::make('name')
                ->label('Name'),
            Tables\Columns\TextColumn::make('corporate_name')
                ->label('Corporate Name'),
            Tables\Columns\TextColumn::make('address')
                ->label('Address'),
            Tables\Columns\TextColumn::make('postal_code')
                ->label('Postal Code'),
            Tables\Columns\TextColumn::make('locality')
                ->label('Locality'),
            Tables\Columns\TextColumn::make('province')
                ->label('Province'),
            Tables\Columns\TextColumn::make('discount')
                ->label('Discount')
                ->numeric()
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
