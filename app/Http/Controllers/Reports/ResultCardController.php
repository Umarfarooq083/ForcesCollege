<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\ResultCardRequest;
use App\Models\ExamType;
use App\Services\Reports\ResultCardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Inertia\Response;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;


class ResultCardController extends Controller
{
    protected $resultCardService;
    public function __construct(ResultCardService $resultCardService)
    {
        $this->resultCardService = $resultCardService;
    }

    public function resultSheet(): Response
    {
        $data = $this->resultCardService->resultSheet();
        return Inertia::render('Reports/ResultSheet', $data);
    }

    public function resultCard(ResultCardRequest $request)
    {   
        $data = $this->resultCardService->resultCard($request->validated(), $request->allterms);
        $examMarksDetails = $examMarksDetails = collect(data_get($data, 'ExamStudents.Student.ExamMarksDetails', []));
        $data['obtaint_marks'] = $examMarksDetails->sum('Marks');
        $data['Exam'] = ExamType::where('id', $request->examid)->where('tenant_id', tenant('id'))->first();
       
        $data['total_marks'] = $examMarksDetails->sum(function ($marks) {
            return $marks?->examMarks?->ExamSubject?->MarksMax ?? 0;
        });

        $percentage = $data['total_marks'] > 0 ? round(($data['obtaint_marks'] / $data['total_marks']) * 100, 2) : 0;

        $grade = collect($data['Grads'])->first(function ($grade) use ($percentage) {
            return $percentage >= $grade->PercentFrom && $percentage <= $grade->PercentUpt;
        });

        $data['obtained_grade'] = $grade?->GradeName ?? 'N/A';
        $data['percentage'] = $percentage;
        $collection = collect($data['ResultData'])->values();
        $data['FinalMarksDetails'] = $collection->last();
        return view('resultsheet.resultcard', $data);

    }

    public function getExamTypes(Request $request): Collection
    {     
        $session = fetchCurrentSession();
        return ExamType::where('ExamTermId', $request->examtermid)->where('IsActive', 1)->where('SessionId', $session->id)->where('tenant_id', tenant('id'))->get();   
    }

    public function getExamStudents(ResultCardRequest $request)
    {
        return $this->resultCardService->getExamStudents($request->validated());  
    }
}
