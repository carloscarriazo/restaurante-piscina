<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Constants\ValidationRules;
use App\Constants\MessageConstants;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\CategoryService;

class ProductManager extends Component
{
    use WithFileUploads, WithPagination;

    // Propiedades del componente
    public $products;
    public $categories;
    public $units;

    // Propiedades del formulario
    public $form = [];
    public $product;
    public $editing = false;
    public $showModal = false;

    // Propiedades para crear nueva categoría
    public $showCategoryModal = false;
    public $newCategoryName = '';
    public $newCategoryDescription = '';

    // Propiedades de filtros
    public $search = '';
    public $categoryFilter = '';
    public $availableFilter = '';    // Reglas de validación
    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.description' => ValidationRules::NULLABLE_STRING_1000,
        'form.price' => 'required|numeric|min:0|max:99999.99',
        'form.category_id' => 'required|exists:categories,id',
        'form.unit_id' => 'required|exists:units,id',
        'form.stock' => 'nullable|integer|min:0',
        'form.stock_minimo' => 'nullable|integer|min:0',
        'form.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'form.available' => 'boolean',
        'form.active' => 'boolean',
        'newCategoryName' => 'required|string|max:255|unique:categories,nombre',
        'newCategoryDescription' => ValidationRules::NULLABLE_STRING_1000,
    ];

    protected $messages = [
        'form.name.required' => 'El nombre del producto es obligatorio.',
        'form.name.max' => 'El nombre no puede tener más de 255 caracteres.',
        'form.price.required' => 'El precio es obligatorio.',
        'form.price.numeric' => 'El precio debe ser un número.',
        'form.price.min' => 'El precio debe ser mayor o igual a 0.',
        'form.category_id.required' => 'Debe seleccionar una categoría.',
        'form.category_id.exists' => 'La categoría seleccionada no es válida.',
        'form.unit_id.required' => 'Debe seleccionar una unidad de medida.',
        'form.unit_id.exists' => 'La unidad seleccionada no es válida.',
        'form.image.image' => 'El archivo debe ser una imagen.',
        'form.image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
        'form.image.max' => 'La imagen no debe ser mayor a 2MB.',
        'newCategoryName.required' => 'El nombre de la categoría es obligatorio.',
        'newCategoryName.unique' => 'Ya existe una categoría con este nombre.',
        'newCategoryName.max' => 'El nombre no puede tener más de 255 caracteres.',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $productService = app(ProductService::class);

        $filters = [
            'search' => $this->search,
            'category_id' => $this->categoryFilter,
            'available' => $this->availableFilter !== '' ? (bool)$this->availableFilter : null,
        ];

        $this->products = $productService->getFiltered($filters);
        $this->categories = $productService->getCategories();
        $this->units = $productService->getUnits();
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public function updatedCategoryFilter()
    {
        $this->loadData();
    }

    public function updatedAvailableFilter()
    {
        $this->loadData();
    }

    public function create()
    {
        $this->reset(['form', 'editing']);
        $this->form = [
            'name' => '',
            'description' => '',
            'price' => '',
            'category_id' => '',
            'unit_id' => '',
            'stock' => 0,
            'stock_minimo' => 0,
            'available' => true,
            'active' => true,
        ];
        $this->editing = false;
        $this->showModal = true;
    }

    public function edit($productId)
    {
        $productService = app(ProductService::class);
        $this->product = $productService->find($productId);

        if (!$this->product) {
            session()->flash('error', MessageConstants::PRODUCT_NOT_FOUND);
            return;
        }

        $this->form = [
            'name' => $this->product->name,
            'description' => $this->product->description,
            'price' => $this->product->price,
            'category_id' => $this->product->category_id,
            'unit_id' => $this->product->unit_id,
            'stock' => $this->product->stock,
            'stock_minimo' => $this->product->stock_minimo,
            'available' => $this->product->available,
            'active' => $this->product->active,
        ];
        $this->editing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $productService = app(ProductService::class);

        try {
            if ($this->editing) {
                $productService->update($this->product, $this->form);
                session()->flash('message', 'Producto actualizado exitosamente.');
            } else {
                $productService->create($this->form);
                session()->flash('message', 'Producto creado exitosamente.');
            }

            $this->showModal = false;
            $this->loadData();

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete($productId)
    {
        $productService = app(ProductService::class);
        $product = $productService->find($productId);

        if (!$product) {
            session()->flash('error', MessageConstants::PRODUCT_NOT_FOUND);
            return;
        }

        try {
            $productService->delete($product);
            session()->flash('message', 'Producto eliminado exitosamente.');
            $this->loadData();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleAvailability($productId)
    {
        $productService = app(ProductService::class);
        $product = $productService->find($productId);

        if (!$product) {
            session()->flash('error', MessageConstants::PRODUCT_NOT_FOUND);
            return;
        }

        $updatedProduct = $productService->toggleAvailability($product);
        $status = $updatedProduct->available ? 'disponible' : 'no disponible';
        session()->flash('message', "Producto marcado como {$status}.");

        $this->loadData();
    }

    public function toggleActive($productId)
    {
        $productService = app(ProductService::class);
        $product = $productService->find($productId);

        if (!$product) {
            session()->flash('error', MessageConstants::PRODUCT_NOT_FOUND);
            return;
        }

        $updatedProduct = $productService->toggleActive($product);
        $status = $updatedProduct->active ? 'activo' : 'inactivo';
        session()->flash('message', "Producto marcado como {$status}.");

        $this->loadData();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['form', 'editing', 'product']);
    }

    public function openCategoryModal()
    {
        $this->reset(['newCategoryName', 'newCategoryDescription']);
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->reset(['newCategoryName', 'newCategoryDescription']);
    }

    public function createCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255|unique:categories,nombre',
            'newCategoryDescription' => 'nullable|string|max:1000',
        ]);

        try {
            $categoryService = app(CategoryService::class);
            $categoryData = [
                'nombre' => $this->newCategoryName,
                'descripcion' => $this->newCategoryDescription,
                'is_active' => true,
            ];

            $newCategory = $categoryService->create($categoryData);

            // Recargar las categorías
            $this->loadData();

            // Seleccionar automáticamente la nueva categoría
            $this->form['category_id'] = $newCategory->id;

            // Cerrar modal y mostrar mensaje
            $this->closeCategoryModal();
            session()->flash('message', 'Categoría creada exitosamente.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la categoría: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.product-manager');
    }
}
