<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\On;

class CategoryManager extends Component
{
    public $search = '';
    public $showCategoryForm = false;
    public $categoryId = null;
    public $nombre = '';
    public $descripcion = '';
    public $isEditMode = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre de la categoría es obligatorio',
        'nombre.max' => 'El nombre no puede exceder 255 caracteres',
        'descripcion.max' => 'La descripción no puede exceder 500 caracteres',
    ];

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, function($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->withCount('productos')
            ->orderBy('nombre')
            ->get();

        return view('livewire.category-manager', [
            'categories' => $categories,
        ]);
    }

    public function createCategory()
    {
        $this->resetForm();
        $this->showCategoryForm = true;
        $this->isEditMode = false;
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->nombre = $category->nombre;
        $this->descripcion = $category->descripcion;
        $this->showCategoryForm = true;
        $this->isEditMode = true;
    }

    public function saveCategory()
    {
        $this->validate();

        try {
            if ($this->isEditMode) {
                $category = Category::findOrFail($this->categoryId);
                $category->update([
                    'nombre' => $this->nombre,
                    'descripcion' => $this->descripcion,
                ]);
                session()->flash('success', 'Categoría actualizada exitosamente');
            } else {
                Category::create([
                    'nombre' => $this->nombre,
                    'descripcion' => $this->descripcion,
                    'activo' => true,
                ]);
                session()->flash('success', 'Categoría creada exitosamente');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la categoría: ' . $e->getMessage());
        }
    }

    public function toggleActive($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->update([
                'activo' => !$category->activo
            ]);

            $status = $category->activo ? 'activada' : 'desactivada';
            session()->flash('success', "Categoría {$status} exitosamente");
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    public function deleteCategory($id)
    {
        try {
            $category = Category::findOrFail($id);

            // Verificar si tiene productos asociados
            if ($category->productos()->count() > 0) {
                session()->flash('error', 'No se puede eliminar la categoría porque tiene productos asociados');
                return;
            }

            $category->delete();
            session()->flash('success', 'Categoría eliminada exitosamente');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la categoría: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showCategoryForm = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->categoryId = null;
        $this->nombre = '';
        $this->descripcion = '';
    }
}
