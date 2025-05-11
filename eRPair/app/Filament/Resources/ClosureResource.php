<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClosureResource\Pages;
use App\Filament\Resources\ClosureResource\RelationManagers;
use App\Models\Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClosureResource extends Resource
{
    protected static ?string $model = Closure::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $label = 'Cierres';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('test')
                ->label('Test')
                ->required(),
            Forms\Components\Textarea::make('comment')
                ->label('Comment')
                ->required(),
            Forms\Components\TextInput::make('humidity')
                ->label('Humidity')
                ->required(),
            Forms\Components\Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->required(),
            ]);
        }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('test')
                ->label('Test')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('comment')
                ->label('Comment')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('humidity')
                ->label('Humidity')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('user.name')
                ->label('User')
                ->sortable()
                ->searchable(),
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
            'index' => Pages\ListClosures::route('/'),
            'create' => Pages\CreateClosure::route('/create'),
            'edit' => Pages\EditClosure::route('/{record}/edit'),
        ];
    }
}
