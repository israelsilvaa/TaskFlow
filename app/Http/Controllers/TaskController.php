<?php

namespace App\Http\Controllers;

use App\Http\Requests\taskFormRequest;
use Exception;
use App\Models\Task;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function viewTarefas()
    {
        return view('app.user.tasks');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $task = Task::where('user_id', $user->id)->paginate(3); 
        return response()->json($task, 201);
        // return response()->json([
        //     "success" => [
        //         "status" => "201", "title" => "OK", "detail" => $task
        //     ]
        // ], 201);
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
    public function store(taskFormRequest $request)
    {
        // dd($request->all());
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $newTask = new Task();
            $newTask['user_id'] = $user->id;
            $newTask->fill($request->validated());

            if ($newTask->save()) {
                return response()->json([
                    "success" => [
                        "status" => "201", "title" => "Created", "detail" => $newTask
                    ]
                ], 201);
            } else {
                return response()->json([
                    "error" => [
                        "status" => "500", "title" => "Internal Server Error", "detail" => "Erro ao salvar"
                    ]
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                "error" => [
                    "status" => "500",
                    "title" => "Internal Server Error",
                    "detail" => $e->getMessage(),
                ]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
