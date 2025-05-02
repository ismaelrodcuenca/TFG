<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderClosureResource\Pages;
use App\Filament\Resources\WorkOrderClosureResource\RelationManagers;
use App\Models\WorkOrderClosure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkOrderClosureResource extends Resource
{
    protected static ?string $model = WorkOrderClosure::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            'index' => Pages\ListWorkOrderClosures::route('/'),
            'create' => Pages\CreateWorkOrderClosure::route('/create'),
            'edit' => Pages\EditWorkOrderClosure::route('/{record}/edit'),
        ];
    }
}
