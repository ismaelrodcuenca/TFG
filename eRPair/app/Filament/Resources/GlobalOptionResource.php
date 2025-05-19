<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GlobalOptionResource\Pages;
use App\Filament\Resources\GlobalOptionResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\GlobalOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GlobalOptionResource extends Resource
{
    protected static ?string $model = GlobalOption::class;

    protected static ?string $label = 'Datos Fiscales';
    protected static ?string $navigationGroup = 'Recursos';

    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isAdmin();
    }
    protected static ?string $navigationIcon = 'heroicon-o-document-currency-euro';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            'index' => Pages\ListGlobalOptions::route('/'),
            'create' => Pages\CreateGlobalOption::route('/create'),
            'edit' => Pages\EditGlobalOption::route('/{record}/edit'),
        ];
    }
}
