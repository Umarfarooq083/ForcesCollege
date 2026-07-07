<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssignClassTeacherRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        $id = $request->id; 
        return [
            'ClassId' => ['required'],
            'SectionId' => ['required'],
            'StaffId' => [
                'required',
                Rule::unique('assign_class_teacher')
                    ->ignore($id)
                    ->where(function ($query) {
                        return $query->where('tenant_id', tenant('id'))
                        ->whereNull('deleted_at');
                    }),
            ],
            'SectionId' => [
                'required',
                Rule::unique('assign_class_teacher')
                    ->ignore($id)
                    ->where(function ($query) {
                        return $query->where('ClassId', $this->ClassId)
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at');  
                    }),
            ],

        ];
    }
}
