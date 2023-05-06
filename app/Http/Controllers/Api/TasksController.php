<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Task::with('tasks')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {   
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        
    }

    public function updateStatus(Request $request, $id)
    {
        // echo $request->id;
        $task = Task::find($id);
        if(!$task){
            return response()->json([
                'success' => false,
                'message' => 'Task not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        
        $task->completed = !$task->completed;
        $task->save();


        return response()->json([
            'message' => 'Task status updated successfully.',
            'data' => $task,
        ]);
    }
}
