<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\PhoneBook;
use App\Models\PhonebookGroup;

class PhoneBookGroupService extends Controller
{
    public function index($request)
    {
        $query =  PhonebookGroup::where('tenant_id', tenant('id'));
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        return [
            'phonebook_groups' => $query->paginate(25)->withQueryString(),
        ];
    }

    public function submit($validated)
    {
        PhonebookGroup::create([
            'tenant_id'   => tenant('id'),
            'school_id'   => 1,
            'is_active'   => 1,
            'session_id'  => fetchCurrentSession()->id,
            'created_by'  => auth()->id(),
            'name'        => $validated['name'],
        ]);
    }

    public function update($validated)
    {
        $group = PhonebookGroup::where('tenant_id', tenant('id'))->findOrFail($validated['id']);
        $group->update([
            'name'          => $validated['name'],
            'modified_by'   => auth()->id(),
        ]);
    }
}