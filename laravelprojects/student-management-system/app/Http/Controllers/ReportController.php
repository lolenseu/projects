<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use DB;

class ReportController extends Controller
{
    public function index()
    {
        $reports = $this->generateDepartmentReports();
        return view('report', compact('reports'));
    }

    public function getContent()
    {
        $reports = $this->generateDepartmentReports();
        return view('report', compact('reports'));
    }

    private function generateDepartmentReports()
    {
        $departments = ['GS', 'CTE', 'CAS', 'CBME'];
        $reports = [];

        foreach ($departments as $dept) {
            $studentCount = DB::table('students')
                ->where('department', $dept)
                ->count();

            $teacherCount = DB::table('teachers')
                ->where('department', $dept)
                ->count();

            $subjectCount = DB::table('subjects')
                ->where('department', $dept)
                ->count();

            $reports[] = [
                'department' => $dept,
                'student_count' => $studentCount,
                'teacher_count' => $teacherCount,
                'subject_count' => $subjectCount,
            ];
        }

        return $reports;
    }
}