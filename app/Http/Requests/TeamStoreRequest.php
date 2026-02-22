<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamStoreRequest extends FormRequest
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
            'name'      => 'required|string|max:255|unique:teams,name',
            'power'     => 'required|integer|min:0|max:100',
            'active'    => 'required|boolean',
        ];
    }

    /**
     * Get the custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // name
            'name.required' => 'Team name is required!',
            'name.string' => 'Team name must be text!',
            'name.max' => 'Team name must be maximum 255 characters long!',
            'name.unique' => 'Team already exists!',

            // power
            'power.required' => 'Team strength is required!',
            'power.integer' => 'Team strength must be number!',
            'power.min' => 'Team strength must be minimum 0!',
            'power.max' => 'Team strength must be maximum 100!',

            // active
            'active.required' => 'Please select if team is active or not!',
            'active.boolean' => 'Please select if team is active or not!',
        ];
    }
}
