<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhoneBookGroupRequest;
use App\Models\PhoneBook;
use App\Models\PhonebookGroup;
use App\Services\PhoneBookGroupService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PhonebookGroupController extends Controller
{
    protected $phoneBookGroupService;
    public function __construct(PhoneBookGroupService $phoneBookGroupService)
    {
        $this->phoneBookGroupService = $phoneBookGroupService;
    }

    public function index(Request $request)
    {   
        $data = $this->phoneBookGroupService->index($request);
        return Inertia::render('PhonebookGroup/List', $data);
    }

    public function submit(PhoneBookGroupRequest $request)
    {
        $this->phoneBookGroupService->submit($request->validated());
        return $this->redirectSuccess('Phonebook group added successfully!','phonebookgroup.index');
    }

    public function update(PhoneBookGroupRequest $request)
    {
        $this->phoneBookGroupService->update($request->validated());
        return $this->redirectSuccess('Phonebook group updated successfully!','phonebookgroup.index');
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:phonebook_groups,id',
        ]);

        $exist = PhoneBook::where(['tenant_id' => tenant('id'), 'phonebook_group_id' => $validated['id']])->exists();
        if($exist)
        {
            return $this->redirectError("This group associated with phonebook thats way can't delete it!","phonebookgroup.index");
        }
        PhonebookGroup::where(['tenant_id' => tenant('id'), 'id' => $validated['id']])->delete();
        return $this->redirectSuccess('Phonebook group deleted successfully!','phonebookgroup.index');
    }
}
