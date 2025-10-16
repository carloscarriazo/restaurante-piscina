<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ingredient;
use App\Models\Unit;

class IngredientManager extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $ingredient_id;
    public $name;
    public $unit;
    public $current_stock = 0;
    public $minimum_stock = 0;
    public $cost_per_unit = 0;
    public $supplier;
    public $is_active = true;

    // Propiedades de control
    public $isOpen = false;
    public $search = '';
    public $filterStock = 'all'; // all, low_stock, out_of_stock

    protected $rules = [
        'name' => 'required|string|max:255',
        'unit' => 'required|string|max:50',
        'current_stock' => 'required|numeric|min:0',
        'minimum_stock' => 'required|numeric|min:0',
        'cost_per_unit' => 'required|numeric|min:0',
        'supplier' => 'nullable|string|max:255',
        'is_active' => 'boolean'
    ];

    protected $messages = [
        'name.required' => 'El nombre del ingrediente es obligatorio',
        'unit.required' => 'La unidad de medida es obligatoria',
        'current_stock.required' => 'El stock actual es obligatorio',
        'minimum_stock.required' => 'El stock mínimo es obligatorio',
        'cost_per_unit.required' => 'El costo por unidad es obligatorio',
    ];

    public function render()
    {
        $ingredients = Ingredient::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('supplier', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStock === 'low_stock', function ($query) {
                $query->whereColumn('current_stock', '<=', 'minimum_stock')
                    ->where('current_stock', '>', 0);
            })
            ->when($this->filterStock === 'out_of_stock', function ($query) {
                $query->where('current_stock', '<=', 0);
            })
            ->orderBy('name')
            ->paginate(10);

        $lowStockCount = Ingredient::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->count();

        $outOfStockCount = Ingredient::where('current_stock', '<=', 0)->count();

        return view('livewire.ingredient-manager', [
            'ingredients' => $ingredients,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $this->ingredient_id = $id;
        $this->name = $ingredient->name;
        $this->unit = $ingredient->unit;
        $this->current_stock = $ingredient->current_stock;
        $this->minimum_stock = $ingredient->minimum_stock;
        $this->cost_per_unit = $ingredient->cost_per_unit;
        $this->supplier = $ingredient->supplier;
        $this->is_active = $ingredient->is_active;
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        try {
            Ingredient::updateOrCreate(
                ['id' => $this->ingredient_id],
                [
                    'name' => $this->name,
                    'unit' => $this->unit,
                    'current_stock' => $this->current_stock,
                    'minimum_stock' => $this->minimum_stock,
                    'cost_per_unit' => $this->cost_per_unit,
                    'supplier' => $this->supplier,
                    'is_active' => $this->is_active
                ]
            );

            session()->flash('message', $this->ingredient_id ? 'Ingrediente actualizado exitosamente.' : 'Ingrediente creado exitosamente.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el ingrediente: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $ingredient = Ingredient::findOrFail($id);

            // Verificar si el ingrediente está siendo usado en recetas
            if ($ingredient->recipes()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el ingrediente porque está siendo usado en recetas.');
                return;
            }

            $ingredient->delete();
            session()->flash('message', 'Ingrediente eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el ingrediente: ' . $e->getMessage());
        }
    }

    public function adjustStock($id, $adjustment)
    {
        try {
            $ingredient = Ingredient::findOrFail($id);
            $newStock = $ingredient->current_stock + $adjustment;

            if ($newStock < 0) {
                session()->flash('error', 'El stock no puede ser negativo.');
                return;
            }

            $ingredient->update(['current_stock' => $newStock]);

            // Crear movimiento de stock
            $ingredient->stockMovements()->create([
                'type' => $adjustment > 0 ? 'entrada' : 'salida',
                'quantity' => abs($adjustment),
                'reason' => $adjustment > 0 ? 'Ajuste manual - Entrada' : 'Ajuste manual - Salida',
                'user_id' => auth()->id()
            ]);

            session()->flash('message', 'Stock ajustado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al ajustar el stock: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->ingredient_id = null;
        $this->name = '';
        $this->unit = '';
        $this->current_stock = 0;
        $this->minimum_stock = 0;
        $this->cost_per_unit = 0;
        $this->supplier = '';
        $this->is_active = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStock()
    {
        $this->resetPage();
    }
}

