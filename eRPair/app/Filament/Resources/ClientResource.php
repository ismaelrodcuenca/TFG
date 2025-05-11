<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ClientResource\RelationManagers\DeviceRelationManager;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $label = 'Cliente';

    /*
     * 
     * PUTO CENTRATE. CREA EL RELATIONSHIP MANAGER CON DISPOSITIVOS. 
     * - LA VALIDACION DEL DOCUMENTO NO PUTO FUNCIONA.
     * 
     */

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('document_type_id')
                    ->label('Tipo de documento')
                    ->relationship('documentType', 'name')
                    ->required(),
                Forms\Components\TextInput::make('document')
                    ->label('Documento')
                    ->required()
                    ->unique()
                    ->rules([
                        fn(): Closure => function (string $attribute, $value, Closure $fail) {
                            $documentTypeId = request()->input('document_type_id') ?? null;

                            if ($documentTypeId === 1) {
                                $value = strtoupper($value);
                                if (!preg_match('/^[0-9]{8}[A-Z]$/', $value)) {
                                    return $fail("El documento no tiene el formato correcto.");
                                }
                                if (strlen($value) > 9) {
                                    return $fail("El documento no puede tener más de 9 caracteres.");
                                }

                                $numbers = substr($value, 0, 8);
                                $letter = substr($value, -1);
                                $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';

                                if ($letter !== $letters[$numbers % 23]) {
                                    return $fail("El documento no es válido.");
                                }
                            }if ($documentTypeId === 2) {
                                if (!preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $value)) {
                                    return $fail("El NIE no tiene el formato correcto.");
                                }

                                $prefix = ['X' => '0', 'Y' => '1', 'Z' => '2'][substr($value, 0, 1)];
                                $numbers = $prefix . substr($value, 1, 7);
                                $letter = substr($value, -1);
                                $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';

                                if ($letter !== $letters[$numbers % 23]) {
                                    return $fail("El NIE no es válido.");
                                }
                            }
                        }
                    ]),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                Forms\Components\TextInput::make('surname')
                    ->label('Apellido')
                    ->required(),
                Forms\Components\TextInput::make('surname2')
                    ->label('Segundo apellido')
                    ->nullable(),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Numero de telefono')
                    ->required()
                    ->tel(),
                Forms\Components\TextInput::make('phone_number_2')
                    ->label('Número de teléfono secundario')
                    ->nullable()
                    ->tel(),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Código postal')
                    ->nullable(),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Tipo de documento')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('document')
                    ->label('Documento')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('surname')
                    ->label('Apellido')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('surname2')
                    ->label('Segundo apellido')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Número de teléfono')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number_2')
                    ->label('Número de teléfono secundario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->label('Código postal')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DeviceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
