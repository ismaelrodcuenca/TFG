<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClosureRelationManager extends RelationManager
{
    protected static string $relationship = 'closure';
    
     
    public function form(Form $form): Form
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

    
    public function table(Table $table): Table
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
