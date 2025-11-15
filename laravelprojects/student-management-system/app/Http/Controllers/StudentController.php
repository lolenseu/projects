<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $students = DB::table('students')
            ->where('user_id', Auth::id())
            ->get();

        $subjects = DB::table('subjects')->get();

        return view('student', compact('students', 'subjects'));
    }

    public function store(Request $req)
    {
        $req->validate([
            'student_id' => 'required',
            'name' => 'required',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'department' => 'required|string',
        ]);

        $subjects = $req->input('subjects', []);

        DB::table('students')->insert([
            'user_id' => Auth::id(),
            'student_id' => $req->student_id,
            'name' => $req->name,
            'birthday' => $req->birthday,
            'address' => $req->address,
            'phone' => $req->phone,
            'email' => $req->email,
            'department' => $req->department,
            'subjects' => !empty($subjects) ? json_encode($subjects) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Student added successfully!');
    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'student_id' => 'required',
            'name' => 'required',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'department' => 'required|string',
        ]);

        $student = DB::table('students')
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (! $student) {
            return back()->withErrors('Student not found or not authorized.');
        }

        $subjects = $req->input('subjects', []);

        DB::table('students')
            ->where('id', $id)
            ->update([
                'student_id' => $req->student_id,
                'name' => $req->name,
                'birthday' => $req->birthday,
                'address' => $req->address,
                'phone' => $req->phone,
                'email' => $req->email,
                'department' => $req->department,
                'subjects' => !empty($subjects) ? json_encode($subjects) : null,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Student updated successfully!');
    }

    public function destroy($id)
    {
        DB::table('students')
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->delete();

        $maxId = DB::table('students')
            ->where('user_id', Auth::id())
            ->max('id') ?? 0;
        $nextId = $maxId + 1;

        DB::statement("ALTER TABLE students AUTO_INCREMENT = $nextId");

        return redirect()->back()->with('success', 'Student deleted successfully!');
    }
}