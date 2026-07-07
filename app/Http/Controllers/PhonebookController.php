<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhoneBookRequest;
use App\Models\PhoneBook;
use App\Models\PhonebookGroup;
use App\Services\PhoneBookService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PhonebookController extends Controller
{

    protected $phoneBookService;
    public function __construct(PhoneBookService $phoneBookService)
    {
        $this->phoneBookService = $phoneBookService;
    }

    public function index(Request $request)
    {   
        $data = $this->phoneBookService->index($request);
        return Inertia::render('PhoneBook/List', $data);
    }

    public function create()
    {
        $data['phonebook_group'] = PhonebookGroup::where('tenant_id', tenant('id'))->get();
        return Inertia::render('PhoneBook/Create', $data);
    }


    public function submit(PhoneBookRequest $request){
        
        $this->phoneBookService->submit($request->validated());
        return $this->redirectSuccess('Phone book contact created successfully!','phonebook.index');
    }

    public function edit(Request $request)
    {
        $data = $this->phoneBookService->edit($request);
        return Inertia::render('PhoneBook/Edit', $data);
    }

    public function update(PhoneBookRequest $request){

        $this->phoneBookService->update($request);
        return $this->redirectSuccess('Phone book contact updated successfully!','phonebook.index');
    }

    public function delete(Request $request)
    {   
        $validated = $request->validate([
            'id' => 'required|integer|exists:phone_books,id',
        ]);
        PhoneBook::where(['tenant_id' => tenant('id'), 'id' => $validated['id']])->delete();
        return $this->redirectSuccess('Phone book contact deleted successfully!','phonebook.index');
    }
}
