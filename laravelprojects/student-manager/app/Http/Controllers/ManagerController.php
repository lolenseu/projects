<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    public function index() {
        $managers = DB::table('managers')
            ->where('user_id', Auth::id())
            ->get();

        return view('manager', compact('managers'));
    }

    public function store(Request $req) {
        $req->validate([
            'student_id' => 'required',
            'name' => 'required',
            'birthday' => 'required|date',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'department' => 'required|string',
        ]);

        DB::table('managers')->insert([
            'user_id' => Auth::id(),
            'student_id' => $req->student_id,
            'name' => $req->name,
            'birthday' => $req->birthday,
            'address' => $req->address,
            'phone' => $req->phone,
            'email' => $req->email,
            'department' => $req->department,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Student added successfully!');
    }

    public function update(Request $req, $id) {
        $req->validate([
            'student_id' => 'required',
            'name' => 'required',
            'birthday' => 'required|date',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'department' => 'required|string',
        ]);

        $manager = DB::table('managers')
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$manager) {
            return back()->withErrors('Student not found or not authorized.');
        }

        DB::table('managers')
            ->where('id', $id)
            ->update([
                'student_id' => $req->student_id,
                'name' => $req->name,
                'birthday' => $req->birthday,
                'address' => $req->address,
                'phone' => $req->phone,
                'email' => $req->email,
                'department' => $req->department,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Student updated successfully!');
    }

    public function destroy($id) {
        DB::table('managers')
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->delete();

        $maxId = DB::table('managers')
            ->where('user_id', Auth::id())
            ->max('id') ?? 0;
        $nextId = $maxId + 1;

        DB::statement("ALTER TABLE managers AUTO_INCREMENT = $nextId");

        return redirect()->back()->with('success', 'Student deleted successfully!');
    }
}