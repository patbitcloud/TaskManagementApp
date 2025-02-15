<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'all') {
                $query->whereNotNull('status');
            } else {
                $query->where('status', $request->status);
            }
        }
        $tasks = $query->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        // Find the admin user (assuming email is unique)
        $admin = User::where('email', 'admin@example.com')->first();

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'assigned_user_id' => $request->assigned_user_id,
            'created_by_user_id' => $admin ? $admin->id : Auth::id(), // Assign to admin or fallback to logged-in user,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        $users = User::all(); // Fetch users for the assigned_user_id dropdown
        return view('tasks.edit', compact('task', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $task->update($request->all());

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
