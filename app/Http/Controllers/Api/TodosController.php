<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TodosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        
        // Check if user is an admin
        // if (Auth::user()->isAdmin()) {
            // Return admin data
        // }

        // Check if user is a store executive
        // if (Auth::user()->isStoreExecutive()) {
            // Return store executive data
        // }
        return Todo::with('tasks')->where('user_id',Auth::user()->id)->get();
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
            'task_name.*' => 'required|min:2|max:100',
        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $todo = Todo::create([
            'name' => $request->name,
            'user_id' => Auth::user()->id,
        ]);

        foreach($request->task_name as $key => $single_task_name){

            $todo->tasks()->create([
                'name' => $request->task_name[$key],
                'completed' => false
            ]);

        }

        return response()->json([
            'message' => 'Todo created successfully.',
            'data' => $todo,
        ], Response::HTTP_CREATED);

        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {   
        $item = Todo::find($id);
        if(!$item){
            return response()->json([
                'success' => false,
                'message' => 'Todo not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json($item);
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
        $todo = Todo::find($id);
        if(!$todo){
            return response()->json([
                'success' => false,
                'message' => 'Todo not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
            'task_name.*' => 'required|min:2|max:100',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $todo->name = $request->name;
        $todo->save();
        
        $todo->tasks()->delete();
        foreach($request->task_name as $key => $single_task_name){
            $complete = 0;
            if(isset($request->task_completed[$key])){
                $complete = $request->task_completed[$key];
            }
            $todo->tasks()->create([
                'name' => $request->task_name[$key],
                'completed' => $complete
            ]);

        }

        return response()->json([
            'message' => 'Todo created successfully.',
            'data' => $todo,
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $item = Todo::find($id);

        if(!$item){
            return response()->json([
                'success' => false,
                'message' => 'Todo not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        $item->delete();

        return response()->json(['message' => 'Todo deleted successfully.'], Response::HTTP_ACCEPTED);
    }
}
