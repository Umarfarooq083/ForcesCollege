<?php

namespace App\Http\Controllers\Class;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRequest;
use App\Models\Classes;
use Illuminate\Http\RedirectResponse;
use App\Models\ClassType;
use App\Services\ClassService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class ClassController extends Controller
{
   protected $ClassService;
   public function __construct(ClassService $ClassService)
   {
      $this->ClassService = $ClassService;
   }

   public function index(Request $request): Response
   {
      $classes = $this->ClassService->index($request);
      return Inertia::render('Classes/List',[
         'classes' =>$classes,
      ]);
   }

   public function create(): Response
   {
      $classtype = ClassType::get();
      return Inertia::render('Classes/Create',[
         'classtype' => $classtype,
      ]);
   }
   
   public function edit(Request $request)
   {
      $classtype = ClassType::get();
      $classesList = Classes::findOrFail($request->id);
      return Inertia::render('Classes/Edit',[
         'classtype' => $classtype,
         'classesList' => $classesList,
      ]);
   }

   public function submit(ClassRequest $request): RedirectResponse
   {
      $this->ClassService->submit($request);
      return $this->redirectSuccess('Class created successfully!', 'class.index');
   }

   public function update(ClassRequest $request): RedirectResponse
   {
      $this->ClassService->update($request);
      return $this->redirectSuccess('Class updated successfully!', 'class.index');
   }
   
   public function delete(Request $request): RedirectResponse
   {
      $this->ClassService->delete($request);
      return $this->redirectSuccess('Class deleted successfully!', 'class.index');
   }

   public function statusUpdate(Request $request, $id): RedirectResponse
   {
      $this->ClassService->statusUpdate($request, $id);
      return $this->redirectSuccess('Class status updated successfully!', 'class.index');
   }
}
