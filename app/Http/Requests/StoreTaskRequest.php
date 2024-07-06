<?php

namespace App\Http\Requests;

use App\Http\Services\ApiServices;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'min:3', 'max:255'],
            'description' => ['required'],
            'status_id' => ['exists:status,id'],
            'due_date' => ['required', 'date'],
        ];
    }

     /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'O campo titulo é obrigatório.',
            'title.min' => 'O campo titulo deve ter no mínimo 3 caracteres.',
            'title.max' => 'O campo titulo deve ter no máximo 255 caracteres.',
            'description.required' => 'O campo descrição é obrigatório.',
            'status_id.exists' => 'O status selecionado é inválido.',
            'due_date.date' => 'A data de entrega deve ser uma data válida.',
            'due_date.required' => 'A data de entrega é obrigatória.',
        ];
    
    }

     /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiServices::statusCode422($validator->errors()));
    }
}
