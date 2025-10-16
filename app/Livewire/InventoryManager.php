<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Constants\ValidationRules;
use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class InventoryManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedIngredient = null;
    public $showModal = false;
    public $modalType = ''; // 'add', 'adjust', 'movement'

    // Campos para formularios
    public $name = '';
    public $description = '';
    public $unit = '';
    public $currentStock = 0;
    public $minimumStock = 0;
    public $costPerUnit = '';
    public $supplier = '';
    public $isActive = true;

    // Para movimientos de stock
    public $movementType = '';
    public $movementQuantity = '';
    public $movementReason = '';

    protected $rules = [
        'name' => ValidationRules::REQUIRED_STRING_255,
        'description' => 'nullable|string',
        'unit' => 'required|string|max:50',
        'currentStock' => 'required|numeric|min:0',
        'minimumStock' => 'required|numeric|min:0',
        'costPerUnit' => 'nullable|numeric|min:0',
        'supplier' => 'nullable|string|max:255',
        'isActive' => 'boolean',
    ];

    public function mount()
    {
        $this->reset();
    }

    public function render()
    {
        $ingredients = Ingredient::with(['stockMovements'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        $lowStockIngredients = Ingredient::where('currentStock', '<=', 'minimumStock')
            ->where('isActive', true)
            ->count();

        return view('livewire.inventory-manager', compact('ingredients', 'lowStockIngredients'));
    }

    public function openAddModal()
    {
        $this->reset(['name', 'description', 'unit', 'currentStock', 'minimumStock', 'costPerUnit', 'supplier', 'isActive']);
        $this->isActive = true;
        $this->modalType = 'add';
        $this->showModal = true;
    }

    public function openEditModal($ingredientId)
    {
        $ingredient = Ingredient::findOrFail($ingredientId);

        $this->selectedIngredient = $ingredient->id;
        $this->name = $ingredient->name;
        $this->description = $ingredient->description;
        $this->unit = $ingredient->unit;
        $this->currentStock = $ingredient->current_stock;
        $this->minimumStock = $ingredient->minimum_stock;
        $this->costPerUnit = $ingredient->cost_per_unit;
        $this->supplier = $ingredient->supplier;
        $this->isActive = $ingredient->is_active;

        $this->modalType = 'edit';
        $this->showModal = true;
    }

    public function openMovementModal($ingredientId)
    {
        $this->selectedIngredient = $ingredientId;
        $this->reset(['movementType', 'movementQuantity', 'movementReason']);
        $this->modalType = 'movement';
        $this->showModal = true;
    }

    public function saveIngredient()
    {
        $this->validate();

        if ($this->modalType === 'add') {
            Ingredient::create([
                'name' => $this->name,
                'description' => $this->description ?: null,
                'unit' => $this->unit,
                'currentStock' => $this->currentStock,
                'minimumStock' => $this->minimumStock,
                'costPerUnit' => empty($this->costPerUnit) ? null : $this->costPerUnit,
                'supplier' => empty($this->supplier) ? null : $this->supplier,
                'isActive' => $this->isActive,
            ]);

            session()->flash('message', 'Ingrediente creado exitosamente.');
        } else {
            $ingredient = Ingredient::findOrFail($this->selectedIngredient);

            $ingredient->update([
                'name' => $this->name,
                'description' => $this->description ?: null,
                'unit' => $this->unit,
                'currentStock' => $this->currentStock,
                'minimumStock' => $this->minimumStock,
                'costPerUnit' => empty($this->costPerUnit) ? null : $this->costPerUnit,
                'supplier' => empty($this->supplier) ? null : $this->supplier,
                'isActive' => $this->isActive,
            ]);

            session()->flash('message', 'Ingrediente actualizado exitosamente.');
        }

        $this->closeModal();
    }

    public function addStock()
    {
        $this->validate([
            'movementQuantity' => 'required|numeric|min:0.001',
            'movementReason' => ValidationRules::REQUIRED_STRING_255,
        ]);

        $ingredient = Ingredient::findOrFail($this->selectedIngredient);

        $ingredient->increaseStock(
            $this->movementQuantity,
            $this->movementReason,
            Auth::id()
        );

        session()->flash('message', 'Stock aumentado exitosamente.');
        $this->closeModal();
    }

    public function subtractStock()
    {
        $this->validate([
            'movementQuantity' => 'required|numeric|min:0.001',
            'movementReason' => ValidationRules::REQUIRED_STRING_255,
        ]);

        $ingredient = Ingredient::findOrFail($this->selectedIngredient);

        try {
            $ingredient->decreaseStock(
                $this->movementQuantity,
                $this->movementReason,
                Auth::id()
            );

            session()->flash('message', 'Stock reducido exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['selectedIngredient', 'modalType']);
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
