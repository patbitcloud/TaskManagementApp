<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user(); // Get logged-in user
        $query = Task::query();

        $query->where('assigned_user_id', $user->id)
            ->orWhereHas('creator', function ($query) use ($user) {
                $query->where('created_by_user_id', $user->id);
            });

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'all') {
                $query->whereNotNull('status');
            } else {
                $query->where('status', $request->status);
                $query1 = Task::where('assigned_user_id', $user->id)
                    ->where('status', $request->status);
                $query2 = Task::whereHas('creator', function ($query) use ($user) {
                    $query->where('created_by_user_id', $user->id);
                })->where('status', $request->status);
                $query = $query1->union($query2);
            }
        }
        $tasks = $query
            ->paginate(10);
        //$tasks = $query->where('created_by_user_id', Auth::id())->paginate(10);

        // If request is from API (expects JSON), return JSON
        if (request()->wantsJson()) {
            return response()->json(['data' => $tasks], 200);
        }

        // Otherwise, return Blade view
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

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'assigned_user_id' => $request->assigned_user_id,
            'created_by_user_id' => Auth::id()
        ]);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Task created successfully.'], 201);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        return response()->json($task, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        $users = User::all(); // Fetch users for the assigned_user_id dropdown
        // If request is from API (expects JSON), return JSON
        if (request()->wantsJson()) {
            return response()->json($task, 200);
        }
        return view('tasks.edit', compact('task', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);
    
        $task->update($validatedData);

        // If request is from API (expects JSON), return JSON
        if (request()->wantsJson()) {
            return response()->json(['message' => 'Task updated successfully.'], 200);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        // If it's an API request, return a 204 response
        if (request()->wantsJson()) {
            return response()->noContent();
        }

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
