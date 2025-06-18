<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'main_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4048',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4048',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'in_stock' => 'nullable|boolean',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
        ];
    }
}
