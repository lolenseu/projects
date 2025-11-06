<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    public function index() {
        $todos = Todo::where('user_id', Auth::id())->get();
        return view('todo', compact('todos'));
    }

    public function store(Request $req) {
        $req->validate([
            'task' => 'required',
        ]);

        Todo::create([
            'user_id' => Auth::id(),
            'task' => $req->task,
            'description' => $req->description ?? '',
        ]);

        return back();
    }

    public function update(Request $req, $id) {
        $req->validate([
            'task' => 'required',
            'status' => 'required',
        ]);

        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);

        $todo->update([
            'task' => $req->task,
            'description' => $req->description ?? '',
            'status' => $req->status,
        ]);

        return back();
    }

    public function destroy($id)
    {
        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
        $todo->delete();

        $maxId = Todo::where('user_id', Auth::id())->max('id') ?? 0;
        $nextId = $maxId + 1;
        DB::statement("ALTER TABLE todos AUTO_INCREMENT = $nextId");

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }
}
