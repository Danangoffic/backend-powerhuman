<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048|mimes:png,jpg,jpeg',
        ];
    }

    /**
     * Set message for validation rules that apply to the request.
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be string',
            'name.max' => 'Name must be less than 255 characters',
            'logo.image' => 'Logo must be an image',
            'logo.max' => 'Logo must be less than 2MB',
            'logo.mimes' => 'Logo must be png, jpg, or jpeg',
        ];
    }
}
