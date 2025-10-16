<?php

namespace App\Http\Requests\Api;

use App\Constants\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'table_id' => 'sometimes|exists:tables,id',
            'customer_name' => ValidationRules::NULLABLE_STRING,
            'notes' => ValidationRules::NULLABLE_STRING,
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => ValidationRules::NULLABLE_STRING,
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'table_id.exists' => 'La mesa seleccionada no existe',
            'items.min' => 'Debe agregar al menos un producto',
            'items.*.product_id.required' => 'El producto es obligatorio',
            'items.*.product_id.exists' => 'El producto seleccionado no existe',
            'items.*.quantity.required' => 'La cantidad es obligatoria',
            'items.*.quantity.min' => 'La cantidad debe ser al menos 1',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
