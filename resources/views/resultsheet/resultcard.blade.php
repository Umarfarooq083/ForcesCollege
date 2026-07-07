<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <title>Result Card</title>

    <style>
        body {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            color: #385623;
        }

        .logo_wrapper {
            width: 20%;
        }

        .header_content {
            width: 75%;
        }

        .maintxt {
            font-size: 30px;
        }

        .font24 {
            font-size: 24px;
        }

        .pl100 {
            padding-left: 100px;
        }

        .std_heading {
            width: 25%;
        }

        .std-text {
            width: 75%;
            max-width: 100%;
            overflow: hidden;
            border-bottom: 1px solid #385623;
        }

        .std_details_wrapper .col-md-4,
        .std_details_wrapper .col-md-6 {
            display: inline-flex;
        }

        .attendance {
            width: 100%;
        }

        .sessiontxt .borderBot {
            display: inline-block;
            width: 40%;
        }

        .borderBot {
            border-bottom: 1px solid #385623;
            display: inline-block;
        }

        .std_details_wrapper .borderBot {
            width: 42%;
        }

        .thead-green {
            background: #385623;
            color: #ffffff
        }

        .table-bordered thead td,
        .table-bordered thead th {
            color: #ffffff
        }

        .table-bordered td,
        .table-bordered th {
            text-align: center;
            border: 2px solid #385623;
            font-weight: 700;
            color: #385623;
        }

        .table thead th {
            border-bottom: 2px solid #385623;
        }

        .marks_section .std_heading {
            width: 50%;
        }

        .marks_section .col-md-12,
        .marks_section .col-md-3 {
            display: inline-flex;
        }

        .marks_section .std-text {
            width: 50%;
        }

        .marks_section .ctr_txt {
            width: 25%;
        }

        .marks_section .remark_txt {
            width: 80%;
        }

        .marks_section .borderBot {
            border-bottom: 1px solid #385623;
            width: 100%;
            display: inline-block;
        }

        .distinct_chart .borderBot {
            border-bottom-width: 3px;
        }

        .distinct_chart {
            position: relative;
        }

            .distinct_chart:before {
                content: '';
                display: block;
                position: absolute;
                right: 0;
                top: 0;
                width: 0;
                height: 0;
                border-style: solid;
                border-width: 0 100px 50px 0;
                border-color: transparent #385623 transparent transparent;
            }

            .distinct_chart:after {
                content: '';
                display: block;
                position: absolute;
                left: 0;
                top: 0;
                width: 0;
                height: 0;
                border-style: solid;
                border-width: 50px 100px 0 0;
                border-color: #385623 transparent transparent transparent;
            }

        .chartTbl_wrapper {
            justify-content: space-between;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .chart_tbl {
            width: 75%;
        }

        .chart_grade {
            width: 20%;
        }

        .grade_wraper .chart_tbl {
            width: 95%;
        }

        #grade_tbl.table-bordered td:first-child,
        #grade_tbl.table-bordered td:last-child,
        #moral_tbl.table-bordered td:first-child,
        #development_tbl.table-bordered td:first-child,
        #tafheem_tbl.table-bordered td:first-child {
            text-align: left;
        }

        span.tbl_checkbox {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 1px solid #385623;
        }

        #grade_tbl.table-bordered thead th {
            color: #385623;
        }

        .signature_txt {
            font-weight: 700;
            font-size: 18px;
            text-align: center;
        }

            .signature_txt::before {
                content: '';
                display: block;
            }

        .signature_border {
            border-bottom: 1px solid #385623;
            width: 70%;
            text-align: center;
            display: inline-block;
        }

        .color-white {
            color: #fff !important;
        }

        .mb-5 {
            margin-bottom: 2.5rem !important;
        }
        .table td, .table th {
            padding: 0.5rem;
        }

        .no-print {
            display: block;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                page-break-after: always;
            }
        .row {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .col-md-1 {
            -ms-flex: 0 0 8.333333%;
            flex: 0 0 8.333333%;
            max-width: 8.333333%;
        }
        .col-md-2 {
            -ms-flex: 0 0 16.666667%;
            flex: 0 0 16.666667%;
            max-width: 16.666667%;
        }
        .col-md-3 {
            -ms-flex: 0 0 25%;
            flex: 0 0 25%;
            max-width: 25%;
        }
        .col-md-4 {
            -ms-flex: 0 0 33.333333% !important;
            flex: 0 0 33.333333% !important;
            max-width: 33.333333% !important;
        }
        .col-md-5 {
            -ms-flex: 0 0 41.666667%;
            flex: 0 0 41.666667%;
            max-width: 41.666667%;
        }
        .col-md-6 {
            -ms-flex: 0 0 50%;
            flex: 0 0 50%;
            max-width: 50%;
        }
        .col-md-7 {
            -ms-flex: 0 0 58.333333%;
            flex: 0 0 58.333333%;
            max-width: 58.333333%;
        }
        .col-md-8 {
            -ms-flex: 0 0 66.666667%;
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
        }
        .col-md-9 {
            -ms-flex: 0 0 75%;
            flex: 0 0 75%;
            max-width: 75%;
        }
        .col-md-10 {
            -ms-flex: 0 0 83.333333%;
            flex: 0 0 83.333333%;
            max-width: 83.333333%;
        }
        .col-md-11 {
            -ms-flex: 0 0 91.666667%;
            flex: 0 0 91.666667%;
            max-width: 91.666667%;
        }
        .col-md-12 {
            -ms-flex: 0 0 100%;
            flex: 0 0 100%;
            max-width: 100%;
        }

            .thead-green {
                background-color: #385623 !important;
            }

            .distinct_chart {
                margin-top: 1em !important;
                print-color-adjust: exact;
            }

            .table thead th {
                border-bottom: 2px solid #385623 !important;
                print-color-adjust: exact;
            }

            .table thead {
                font-size: 14px !important;
                print-color-adjust: exact;
            }

            .table-bordered td,
            .table-bordered th {
                text-align: center;
                border: 2px solid #385623 !important;
                font-weight: 700;
                color: #385623 !important;
                print-color-adjust: exact;
            }

            thead.thead-green {
                background: #385623 !important;
                color: #ffffff !important;
                print-color-adjust: exact;
            }

            .table thead th {
                background: #385623 !important;
                color: #ffffff !important;
                print-color-adjust: exact;
            }

            td.color-white {
                background: #385623 !important;
                color: #ffffff !important;
                print-color-adjust: exact;
            }

            .std_details_wrapper.marks_section {
                page-break-after: always;
            }

            .marks_section .std_heading {
                width: 70% !important;
                print-color-adjust: exact;
            }

            #grade_tbl thead th {
                background: #fff !important;
                color: #385623 !important;
                print-color-adjust: exact;
            }

            .marks_section .ctr_txt {
                width: 25% !important;
                print-color-adjust: exact;
            }

            .subject_tbl {
                min-height: 600px !important;
                print-color-adjust: exact;
            }
            .subject_tbl .table td, .subject_tbl .table th {
                padding: 1rem;
                print-color-adjust: exact;
            }
            .signature_wrapper{
                page-break-after: avoid;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-right no-print mt-3 mb-3">
                <button onclick="window.print()" class="btn btn-success">Print Result Card</button>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row align-items-center mb-5">
                <div class="logo_wrapper text-center">
                    <img src="../assets/images/Logo.jpeg" width="100px" alt="School Logo">
                </div>
                <div class="header_content text-left">
                    <div class="maintxt font-weight-bold pl-5">FORCES SCHOOL AND COLLEGE SYSTEM</div>

                    @if($AllTerms)
                        <div class="assementtxt font24 pl100">Report Card - {{ $Exam?->ExamName }} </div>
                    @else
                        <div class="assementtxt font24 pl100">Assessment Report Card</div>
                    @endif
                    
                    <div class="sessiontxt font24 font-weight-bold pl100">Session:<span class="borderBot text-center font-weight-light session_txt"> {{ $Session['name'] ?? '' }} </span></div>
                </div>
            </div>
            <div class="std_details_wrapper mb-5">
                <div class="form-group mb-5">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="std_heading font-weight-bold">Student Name: </div>
                            <div class="std-text text-center stdname_txt"> <span> {{ $ExamStudents?->Student?->FirstName }} - {{ $ExamStudents?->Student?->LastName }} </span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="std_heading font-weight-bold">Father's Name: </div>
                            <div class="std-text text-center stdfather_txt"> <span> {{ $ExamStudents?->Student?->FatherName }} </span></div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-5">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="std_heading font-weight-bold">Class: </div>
                            <div class="std-text text-center stdclass_txt"> <span> {{ $ExamStudents?->Subject?->Class?->ClassName }} </span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="std_heading font-weight-bold">Class Teacher: </div>
                            <div class="std-text text-center stdteacher_txt"> <span> {{ $ExamStudents?->Subject?->Class?->assignedTeacher?->StaffRel?->FirstName }} - {{ $ExamStudents?->Subject?->Class?->assignedTeacher?->StaffRel?->LastName }} </span></div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="std_heading font-weight-bold">Attendance: </div>
                            <div class="attendance d-flex justify-content-around text-center">
                                <span class="borderBot stdobtain_txt">  </span>
                                <span>of</span>
                                <span class="borderBot stdtotal_txt">  </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="std_heading font-weight-bold">Position: </div>
                            <div class="std-text text-center stdposition_txt"> <span> </span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="std_heading font-weight-bold">Date: </div>
                            <div class="std-text text-center stddate_txt"> <span>{{ \Carbon\Carbon::parse($ExamStudents?->Subject?->ExamType?->ResultDeclarationDate)->format('d F Y') }} </span></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="mb-5 subject_tbl">
                <table class="table table-bordered">
                    <thead class="thead-green">
                        {{-- Main Header --}}
                        <tr>
                            <th>Subject</th>

                            @if($AllTerms)
                                @foreach($ExamTypes as $exam)
                                    <th colspan="2" class="text-center">
                                        {{ $exam->ExamName }}
                                    </th>
                                @endforeach
                            @endif

                            <th>Total Marks</th>
                            <th>Obtained Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                        </tr>

                        {{-- Sub Header (Only when AllTerms = true) --}}
                        @if($AllTerms)
                            <tr>
                                <th></th>
                                @foreach($ExamTypes as $exam)
                                    <th>Total</th>
                                    <th>Obt</th>
                                @endforeach
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        @endif
                    </thead>

                    <tbody style="font-size:16px">
                        @foreach($ResultData as $row)
                            <tr>
                                {{-- Subject --}}
                                <td class="column_style {{ $row['Subject'] === 'Total' ? 'thead-green text-white' : '' }}">
                                    {{ $row['Subject'] }}
                                </td>

                                {{-- Exam Terms Columns --}}
                                @if($AllTerms)
                                    @foreach($ExamTypes as $exam)
                                        <td class="column_style">
                                            {{ $row[$exam->ExamName.' Total'] ?? 0 }}
                                        </td>
                                        <td class="column_style">
                                            {{ $row[$exam->ExamName.' Obt'] ?? 0 }}
                                        </td>
                                    @endforeach
                                @endif

                                {{-- Final Columns --}}
                                <td class="column_style">{{ $row['Total Marks'] }}</td>
                                <td class="column_style">{{ $row['Obtained Marks'] }}</td>
                                <td class="column_style">{{ $row['Percentage'] }}</td>
                                <td class="column_style">{{ $row['Grade'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- <div class="subject_tbl mb-5">
                <table class="table table-bordered">
                    <thead class="thead-green">
                        <tr>
                            <th>Subject</th>
                            <th>Total Marks</th>
                            <th>Obtained Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>English</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Urdu</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Mathematics </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Science</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Islamiat</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Social Studies</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Computer</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="thead-green color-white">Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div> --}}

            @if($AllTerms && $isFinalTerm)
                <div class="row mb-5">
                    <div class="col std_heading ctr_txt font-weight-bold">
                        Term Wise Total Marks:    {{ $total_marks_sum }}
                    </div>
                    <div class="col std_heading ctr_txt font-weight-bold text-right">
                        Term Wise Obtained Marks:    {{ $obtained_marks_sum }}
                    </div>
                    <div class="col std_heading ctr_txt font-weight-bold text-right">
                        Term Wise Marks Percentage:    {{ $term_wise_percentage  }}%
                    </div>
                </div>
            @endif

            
            <div class="std_details_wrapper marks_section mb-5">
                <div class="form-group mb-5">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="std_heading font-weight-bold">Total Marks: </div>
                            <div class="std-text text-center totalmarks_txt"> <span> {{ $FinalMarksDetails['Total Marks'] + $total_marks_sum ?? 0 }} </span></div>
                        </div>
                        <div class="col-md-3">
                            <div class="std_heading font-weight-bold">Obtained Marks: </div>
                            <div class="std-text text-center obtainedmarks_txt"> <span> {{ $FinalMarksDetails['Obtained Marks'] + $obtained_marks_sum ?? 0 }} </span></div>
                        </div>

                        @php
                            $totalMarks = ($FinalMarksDetails['Total Marks'] ?? 0) + ($total_marks_sum ?? 0);
                            $obtainedMarks = ($FinalMarksDetails['Obtained Marks'] ?? 0) + ($obtained_marks_sum ?? 0);

                            $percentage = $totalMarks > 0 
                                ? round(($obtainedMarks / $totalMarks) * 100, 2) 
                                : 0;
                        @endphp
                        <div class="col-md-3">
                            <div class="std_heading font-weight-bold">Percentage: </div>
                            <div class="std-text text-center percntage_txt"> <span>{{ $percentage }}%</span></div>
                        </div>
                        <div class="col-md-3">
                            <div class="std_heading font-weight-bold">Grade: </div>
                            <div class="std-text text-center grade_txt"> <span> {{ $FinalMarksDetails['Grade'] ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-5">
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <div class="std_heading ctr_txt font-weight-bold">Class Teacher’s Remarks: </div>
                            <div class="std-text text-left remark_txt tacherremarks_txt"> <span></span></div>
                        </div>
                    </div>
                    <div class="borderBot"></div>
                </div>
                <div class="form-group mb-5">
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <div class="std_heading ctr_txt font-weight-bold">Principal’s Remarks (if any): </div>
                            <div class="std-text text-left remark_txt princpleremarks_txt"> <span></span></div>
                        </div>
                    </div>
                    <div class="borderBot"></div>
                </div>
            </div>
            <div class="distinct_chart mb-5 text-center">
                <h4 class="font-weight-bold borderBot">Distinct Learning Programs Grading Chart</h4>
            </div>
            <div class="tafheem_wraper mb-2">
                <div class="chartTbl_wrapper">
                    <div class="chart_tbl">
                        <table class="table table-bordered" id="tafheem_tbl">
                            <thead class="thead-green">
                                <tr>
                                    <th>Tafheem-e-Deen</th>
                                    <th>A</th>
                                    <th>B</th>
                                    <th>C</th>
                                </tr>
                            </thead>
                            <tbody class="text-left">
                                <tr>
                                    <td>Memorization of Asma-ul-Husna</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                                <tr>
                                    <td>Memorization of Dua(s)</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                                <tr>
                                    <td>Answer to Questions</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="chart_grade">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Excellent</td>
                                    <td>A</td>
                                </tr>
                                <tr>
                                    <td>Good</td>
                                    <td>B</td>
                                </tr>
                                <tr>
                                    <td>Fair</td>
                                    <td>C</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="development_wraper mb-2">
                <div class="chartTbl_wrapper">
                    <div class="chart_tbl">
                        <table class="table table-bordered" id="development_tbl">
                            <thead class="thead-green">
                                <tr>
                                    <th>Personality Development</th>
                                    <th>A</th>
                                    <th>B</th>
                                    <th>C</th>
                                </tr>
                            </thead>
                            <tbody class="text-left">
                                <tr>
                                    <td>Follow directions</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                                <tr>
                                    <td>Practices what they’ve learnt</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                                <tr>
                                    <td>Social Interaction with peers and others</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                                <tr>
                                    <td>Participation in activities</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                                <tr>
                                    <td>Reflects the learning</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="chart_grade">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Excellent</td>
                                    <td>A</td>
                                </tr>
                                <tr>
                                    <td>Good</td>
                                    <td>B</td>
                                </tr>
                                <tr>
                                    <td>Fair</td>
                                    <td>C</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="moral_wraper mb-2">
                <div class="chartTbl_wrapper">
                    <div class="chart_tbl">
                        <table class="table table-bordered" id="moral_tbl">
                            <thead class="thead-green">
                                <tr>
                                    <th>Moral Science</th>
                                    <th>A</th>
                                    <th>B</th>
                                    <th>C</th>
                                </tr>
                            </thead>
                            <tbody class="text-left">
                                <tr>
                                    <td>Follow directions</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                                <tr>
                                    <td>Works independently</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                                <tr>
                                    <td>Practices what they’ve learnt</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                                <tr>
                                    <td>Reflects the learning</td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                    <td><span class="tbl_checkbox"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="chart_grade">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Excellent</td>
                                    <td>A</td>
                                </tr>
                                <tr>
                                    <td>Good</td>
                                    <td>B</td>
                                </tr>
                                <tr>
                                    <td>Fair</td>
                                    <td>C</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="grade_wraper mb-2">
                <div class="chartTbl_wrapper">
                    <div class="chart_tbl">
                        <table class="table table-bordered" id="grade_tbl">
                            <thead>
                                <tr>
                                    <th>Percentage</th>
                                    <th>Grade</th>
                                    <th>Grade Equivalent</th>
                                </tr>
                            </thead>
                            <tbody class="text-left">
                                @foreach ($Grads as $grade)
                                    <tr>
                                        <td>{{ $grade?->PercentFrom }} to {{ $grade?->PercentUpt }}</td>
                                        <td>{{ $grade->GradeName ?? 'N/A' }}</td>
                                        <td>{{ $grade->Description ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="signature_wrapper" style="margin-top:60px">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <div class="signature_border mb-2"></div>
                        <div class="signature_txt">Class Teacher’s Signature</div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="signature_border mb-2"></div>
                        <div class="signature_txt">Principal’s Signature</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
