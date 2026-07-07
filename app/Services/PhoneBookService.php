<?php

namespace App\Services;

use App\Models\PhoneBook;
use App\Models\PhonebookGroup;

class PhoneBookService
{
    public function index($request)
    {
        $query =  PhoneBook::where('tenant_id', tenant('id'))->with('phonebookgroup');
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")->orWhere('contact_no', 'like', "%{$request->search}%");
        }

        return [
            'phonebook_list' => $query->paginate(25)->withQueryString(),
        ];
    }

    public function submit($validated) 
    {   
        $validated['tenant_id'] = tenant('id');
        $validated['school_id'] = 1;
        $validated['is_active'] = 1;
        $validated['session_id'] = fetchCurrentSession()->id;
        $validated['created_by'] = auth()->id();
        PhoneBook::create($validated);
    }

    public function edit($request)
    {
        $data['phonebook_data'] = PhoneBook::where(['tenant_id' => tenant('id'), 'id' => $request->id])->with('phonebookgroup')->first();
        $data['phonebook_group'] = PhonebookGroup::where('tenant_id', tenant('id'))->get();
        return $data;
    }

    public function update($request)
    {
        $validated = $request->validated();
        $phonebook_contact = PhoneBook::where(['tenant_id' => tenant('id'), 'id' => $request->id])->first();
        $phonebook_contact->update([
            'name' => $validated['name'],
            'contact_no' => $validated['contact_no'],
            'phonebook_group_id' => $validated['phonebook_group_id'],
        ]);
    }
}