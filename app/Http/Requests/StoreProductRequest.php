<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Constants\ValidationRules;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Se manejará con middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', ValidationRules::MIN_ZERO, 'max:99999.99'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'stock_quantity' => ['nullable', 'integer', ValidationRules::MIN_ZERO],
            'min_stock' => ['nullable', 'integer', ValidationRules::MIN_ZERO],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_available' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'description.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número válido.',
            'price.min' => 'El precio no puede ser negativo.',
            'price.max' => 'El precio no puede ser mayor a 99999.99.',
            'category_id.required' => 'Debe seleccionar una categoría.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
            'unit_id.required' => 'Debe seleccionar una unidad de medida.',
            'unit_id.exists' => 'La unidad seleccionada no es válida.',
            'stock_quantity.integer' => 'La cantidad en stock debe ser un número entero.',
            'stock_quantity.min' => 'La cantidad en stock no puede ser negativa.',
            'min_stock.integer' => 'El stock mínimo debe ser un número entero.',
            'min_stock.min' => 'El stock mínimo no puede ser negativo.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'image.max' => 'La imagen no puede ser mayor a 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_available' => $this->has('is_available'),
        ]);
    }
}
