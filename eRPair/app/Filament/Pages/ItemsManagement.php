<?php

namespace App\Filament\Pages;

use app\Helpers\PermissionHelper;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Type;
use constants;
use DB;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Tables\Actions\Contracts\HasTable;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Concerns\BuildsQueries;

class ItemsManagement extends Page 
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.items-management';

    protected static ?string $title = 'EN OBRAS';

    public ?int $category_id = null;
    public ?int $type_id = null;
    public ?int $brand = null;
    public ?int $device_model_id = null;
    /**
     * Summary of form
     * @param \Filament\Forms\Form $form
     * @return Form
     * 
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->live()
                    ->schema([
                        Select::make('category_id')
                            ->label('Categoría')
                            ->reactive()
                            ->options(Category::pluck('name', 'id')),
                        Select::make('type_id')
                            ->label('Tipo')
                            ->options(Type::pluck('name', 'id'))
                            ->reactive(),
                        Select::make('brand_id')
                            ->label(constants::MARCA)
                            ->required(fn() => null),
                        Select::make('vice_model_id')
                            ->label(constants::MODELO)
                            ->required(fn() => true)
                            ->reactive()
                            ->options(DeviceModel::pluck('name', 'id')),
                    ])
            ]);
    }

    public function buildQuery(): array
    {
        $statements = [];
        if ($this->category !== null) {
            $statements[] = ['category_id', $this->category];
        }
        if ($this->type !== null) {
            $statements[] = ['type_id', (int) $this->type];
        }
        if ($this->model !== null) {
            $statements[] = ['device_model_id', (int) $this->model];
        }
        $query = Item::query();
        foreach ($statements as $statement) {
            $query->where($statement[0], $statement[1]);
        }
        return $query->get()->toArray();
    }

    public static function table(Infolist $table): Infolist
    {
        return $table

            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('cost')
                    ->label('Costo')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('distributor')
                    ->label('Distribuidor')
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipo')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable(),
            ]);
    }
}
