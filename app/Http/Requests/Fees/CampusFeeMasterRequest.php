<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class CampusFeeMasterRequest extends FormRequest
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

        if ($this->routeIs('feemaster.submit')) {
            return [
                'feetypeid' => 'required|integer|exists:fees_type,id',
                'session' => 'required',
                'fees_master' => 'required|array',
                'fees_master.*.amount' => 'required',
                'fees_master.*.class_id' => [
                    'required',
                    'integer',
                    'exists:classes,id',
                    function ($attribute, $value, $fail) {
                        $index = explode('.', $attribute)[1];
                        $rowId = $this->fees_master[$index]['id'] ?? null;

                        $sessionData = $this->input('session');
                        $sessionId = $sessionData['id'] ?? null;
                        $exists = DB::table('campus_fees_masters')
                            ->where('tenant_id', tenant('id'))
                            ->where('ClassId', $value)
                            ->where('FeesTypeNId', $this->feetypeid)
                            ->where('SessionId', $sessionId)
                            ->whereNull('deleted_at')
                            ->when($rowId, fn ($q) => $q->where('id', '!=', $rowId))
                            ->exists();

                        if ($exists) {
                            $fail('This class is already assigned a fee for this campus.');
                        }
                    },
                ],
            ];
        }

        if ($this->routeIs('feemaster.edit')) {
            return [
                'campus_fee_master_id' => 'required|integer|exists:campus_fees_masters,id',
            ];
        }

        if ($this->routeIs('feemaster.update')) {
            return [
                // 'fee_group_id' => 'required|integer|exists:campus_fees_masters,id',
                'fee_type_id' => 'required|integer|exists:fees_type,id',
                'fees_master' => [
                    'required',
                    'array',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $classIds = array_column($value, 'ClassId');
                        $duplicates = array_diff_assoc($classIds, array_unique($classIds));
                        if (! empty($duplicates)) {
                            $fail('Duplicate class selected in request.');
                        }
                    },
                ],
                // checking uniqueness
                'fees_master.*.ClassId' => [
                    'required',
                    'integer',
                    'exists:classes,id',
                    function ($attribute, $value, $fail) {
                        $index = explode('.', $attribute)[1];
                        $rowId = request()->fees_master[$index]['id'] ?? null;
                        $exists = DB::table('campus_fees_masters')
                            ->where('tenant_id', tenant('id'))
                            ->where('ClassId', $value)
                            ->where('FeesTypeNId', $this->fee_type_id)
                            ->where('SessionId', request()->fees_master[0]['SessionId'])
                            ->whereNull('deleted_at')
                            ->when($rowId, fn ($q) => $q->where('id', '!=', $rowId))
                            ->exists();
                        if ($exists) {
                            $fail('This class already has a fee in this campus.');
                        }
                    },
                ],
            ];
        }

        return [];
    }
}
