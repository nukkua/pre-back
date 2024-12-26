<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCuposDivisionRequest extends FormRequest
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
            'codigo_division' => [
                'required',
                'integer',
                'between:1,10',
                function ($attribute, $value, $fail) {
                    if (\App\Models\CuposDivision::where('codigo_division', $value)
                        ->where('gestion_apertura', $this->input('gestion_apertura'))
                        ->exists()
                    ) {
                        $fail('la division ' . $value . ' ya tiene cupos asignados para esta gestion.');
                    }
                }
            ],
            'cupos' => 'required|integer|min:0',
            'gestion_apertura' => [
                'required',
                'integer',
                function ($value, $attribute, $fail) {
                    if ($value < date('Y')) {
                        $fail('El campo ' . $attribute . ' debe ser igual o mayor que la gestión actual.');
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'codigo_division.required' => 'El campo codigo_division es obligatorio',
            'codigo_division.integer' => 'El campo codigo_division debe ser un entero',
            'codigo_division.between' => 'El campo codigo_division debe estar entre 1 y 10',
            'cupos.required' => 'El campo cupos es obligatorio',
            'cupos.integer' => 'El campo cupos debe ser un entero',
            'cupos.min' => 'El campo cupos debe tener minimo un valor de 0',
            'gestion_apertura.required' => 'El campo gestion_apertura es obligatorio',
            'gestion_apertura.integer' => 'El campo gestion_apertura debe ser un entero',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Something went wrong with the validation.',
            'error' => $validator->errors(),
        ], 422));
    }
}
