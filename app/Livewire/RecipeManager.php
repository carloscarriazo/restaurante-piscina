<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Ingredient;

class RecipeManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedProduct = null;
    public $showModal = false;
    public $modalType = ''; // 'recipe', 'ingredient'

    // Campos para gestión de recetas
    public $recipeIngredients = [];
    public $newIngredientId = '';
    public $newQuantity = '';

    public function mount()
    {
        $this->reset();
    }

    public function render()
    {
        $products = Product::with(['recipes.ingredient'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        $ingredients = Ingredient::where('is_active', true)->orderBy('name')->get();

        return view('livewire.recipe-manager', compact('products', 'ingredients'));
    }

    public function openRecipeModal($productId)
    {
        $product = Product::with(['recipes.ingredient'])->findOrFail($productId);

        $this->selectedProduct = $product;
        $this->recipeIngredients = $product->recipes->map(function ($recipe) {
            return [
                'id' => $recipe->id,
                'ingredient_id' => $recipe->ingredient_id,
                'ingredient_name' => $recipe->ingredient->name,
                'ingredient_unit' => $recipe->ingredient->unit ?? '',
                'quantity' => $recipe->quantity,
                'notes' => $recipe->notes
            ];
        })->toArray();

        $this->modalType = 'recipe';
        $this->showModal = true;
    }

    public function addIngredient()
    {
        $this->validate([
            'newIngredientId' => 'required|exists:ingredients,id',
            'newQuantity' => 'required|numeric|min:0.001',
        ]);

        $ingredient = Ingredient::findOrFail($this->newIngredientId);

        // Verificar si ya existe este ingrediente en la receta
        $exists = collect($this->recipeIngredients)->where('ingredient_id', $this->newIngredientId)->first();

        if ($exists) {
            session()->flash('error', 'Este ingrediente ya está en la receta.');
            return;
        }

        $this->recipeIngredients[] = [
            'id' => null, // Nuevo ingrediente
            'ingredient_id' => $this->newIngredientId,
            'ingredient_name' => $ingredient->name,
            'ingredient_unit' => $ingredient->unit ?? '',
            'quantity' => $this->newQuantity,
            'notes' => ''
        ];

        $this->reset(['newIngredientId', 'newQuantity']);
    }

    public function removeIngredient($index)
    {
        unset($this->recipeIngredients[$index]);
        $this->recipeIngredients = array_values($this->recipeIngredients); // Reindex array
    }

    public function updateIngredientQuantity($index, $quantity)
    {
        if ($quantity > 0) {
            $this->recipeIngredients[$index]['quantity'] = $quantity;
        }
    }

    public function updateIngredientNotes($index, $notes)
    {
        $this->recipeIngredients[$index]['notes'] = $notes;
    }

    public function saveRecipe()
    {
        if (!$this->selectedProduct) {
            return;
        }

        // Eliminar todas las recetas existentes para este producto
        Recipe::where('product_id', $this->selectedProduct->id)->delete();

        // Crear las nuevas recetas
        foreach ($this->recipeIngredients as $ingredient) {
            if ($ingredient['quantity'] > 0) {
                Recipe::create([
                    'product_id' => $this->selectedProduct->id,
                    'ingredient_id' => $ingredient['ingredient_id'],
                    'quantity' => $ingredient['quantity'],
                    'notes' => $ingredient['notes']
                ]);
            }
        }

        session()->flash('message', 'Receta actualizada exitosamente.');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['selectedProduct', 'modalType', 'recipeIngredients', 'newIngredientId', 'newQuantity']);
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function canPrepareProduct($product)
    {
        if ($product->recipes->isEmpty()) {
            return ['can_prepare' => true, 'missing_ingredients' => []];
        }

        $missingIngredients = [];

        foreach ($product->recipes as $recipe) {
            if (!$recipe->ingredient->hasStock($recipe->quantity)) {
                $missingIngredients[] = [
                    'name' => $recipe->ingredient->name,
                    'required' => $recipe->quantity,
                    'available' => $recipe->ingredient->current_stock,
                    'unit' => $recipe->ingredient->unit ?? ''
                ];
            }
        }

        return [
            'can_prepare' => empty($missingIngredients),
            'missing_ingredients' => $missingIngredients
        ];
    }
}
