<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Type;
use constants;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class ItemsManagement extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.items-management';

    protected static ?string $title = 'Catálogo';

    public ?string $category = null;
     public ?string $type    = null;
    public ?string $brand   = null;
    public ?string $model = null;
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
                Select::make('device_model_id')
                    ->label(constants::MODELO)
                    ->required(fn() => true)
                    ->reactive()
                    ->options(DeviceModel::pluck('name', 'id')),

            ]);
    }
    public function query(): array
    {
        return [
            'category' => $this->category = $this->form->getState()['category_id'] ?? '*',
            'type' => $this->type = $this->form->getState()['type_id'] ?? '*',
            'brand' => $this->brand = $this->form->getState()['brand_id'] ?? '*',
            'model' => $this->model = $this->form->getState()['device_model_id'] ?? '*',
        ];
    }

    public function buildQuery(): array
    {

        //Generado completamente por gepeto, arregla esta mierda. 
        $query = [];
        $query = \DB::table('items');

        if ($this->category !== '*' && $this->category !== null) {
            $query->where('category_id', (int) $this->category);
        }

        if ($this->type !== '*' && $this->type !== null) {
            $query->where('type_id', (int) $this->type);
        }

        if ($this->brand !== '*' && $this->brand !== null) {
            $query->where('brand_id', (int) $this->brand);
        }

        if ($this->model !== '*' && $this->model !== null) {
            $query->where('device_model_id', (int) $this->model);
        }

        return $query->get()->toArray();
    }

}
