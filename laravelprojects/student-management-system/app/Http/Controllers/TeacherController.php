<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('subject')->get();
        $subjects = Subject::all();
        return view('teacher', compact('teachers', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|unique:teachers,teacher_id',
            'name' => 'required|string|max:255',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'department' => 'required|in:GS,CTE,CAS,CBME',
            'subject_id' => 'nullable|exists:subjects,id|unique:teachers,subject_id,NULL,id',
        ]);

        Teacher::create($validated);

        return redirect()->route('teacher.index')->with('success', 'Teacher added successfully!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|unique:teachers,teacher_id,' . $id,
            'name' => 'required|string|max:255',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'department' => 'required|in:GS,CTE,CAS,CBME',
            'subject_id' => 'nullable|exists:subjects,id|unique:teachers,subject_id,' . $id . ',id',
        ]);

        $teacher = Teacher::findOrFail($id);
        $teacher->update($validated);

        return redirect()->route('teacher.index')->with('success', 'Teacher updated successfully!');
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();

        return redirect()->route('teacher.index')->with('success', 'Teacher deleted successfully!');
    }

    public function assignTeacher(Request $request, $subjectId)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        Teacher::where('subject_id', $subjectId)->update(['subject_id' => null]);
        $teacher = Teacher::findOrFail($validated['teacher_id']);
        $teacher->subject_id = $subjectId;
        $teacher->save();

        return redirect()->route('subject.index')->with('success', 'Teacher assigned successfully!');
    }

    public function unassignTeacher($teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);
        $teacher->subject_id = null;
        $teacher->save();

        return redirect()->route('subject.index')->with('success', 'Teacher unassigned successfully!');
    }
}