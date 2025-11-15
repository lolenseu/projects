<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('teacher')->get();
        return view('subject', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code' => 'required|unique:subjects,subject_code',
            'subject_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'department' => 'required|in:GS,CTE,CAS,CBME',
        ]);

        Subject::create($validated);

        return redirect()->route('subject.index')->with('success', 'Subject added successfully!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'subject_code' => 'required|unique:subjects,subject_code,' . $id,
            'subject_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'department' => 'required|in:GS,CTE,CAS,CBME',
        ]);

        $subject = Subject::findOrFail($id);
        $subject->update($validated);

        return redirect()->route('subject.index')->with('success', 'Subject updated successfully!');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return redirect()->route('subject.index')->with('success', 'Subject deleted successfully!');
    }
}