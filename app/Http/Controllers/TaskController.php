<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    protected $task;
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
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
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $task = array();

        // selecionar colunas de usuarios relacionados
        if ($request->has('assignedUsers')) {
            $atributos_assignedUsers = $request->assignedUsers;
            $task = $this->task->where('user_id', $user->id)->with('assignedUsers:id,' . $atributos_assignedUsers);
        } else {
            $task = $this->task->where('user_id', $user->id)->with('assignedUsers');
        }

        // selecionar colunas do user criador da task
        if ($request->has('atributos')) {
            $atributos = $request->atributos;
            $task = $task->selectRaw($atributos)->with('user:id,name,role');
        } else {
            $task = $task->with('user:id,name,role');
        }

        // filtra de acordo com as condições passadas por parametro de busca
        if ($request->has('filtro')) {
            $filtros = explode(';', $request->filtro);
            foreach ($filtros as $key => $condicoes) {
                $c = explode(':', $condicoes);

                $task = $task->where($c[0], $c[1], $c[2]);
            }
        }

        $task = $task->paginate(10);

        return response()->json($task, 201);
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
    public function store(StoreTaskRequest $request)
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
    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $task = $this->task->find($id);

            // dd($task, $id);

            // Verifica se a tarefa existe
            if ($task === null) {
                return response()->json([
                    "error" => [
                        "status" => "404",
                        "title" => "Not Found",
                        "detail" => "Registro não encontrado",
                    ]
                ], 404);
            }

            // Verifica se o usuário tem permissão para atualizar a tarefa
            if ($task->user_id !== $user->id) {
                return response()->json([
                    "error" => [
                        "status" => "403",
                        "title" => "Forbidden",
                        "detail" => "Usuário não tem permissão para acessar o registro",
                    ]
                ], 403);
            }

            // Tenta atualizar a tarefa
            if ($task->update($request->all())) {
                return response()->json([
                    "success" => [
                        "status" => "200",
                        "title" => "OK",
                        "detail" => "Atualizado com sucesso"
                    ]
                ], 200);
            }

            return response()->json([
                "error" => ["status" => "500", "title" => "Internal Server Error", "detail" => "Erro ao atualizar"]
            ], 500);

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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $task = $this->task->find($id);

            // Verifica se a tarefa existe
            if ($task === null) {
                return response()->json([
                    "error" => [
                        "status" => "404",
                        "title" => "Not Found",
                        "detail" => "Registro não encontrado",
                    ]
                ], 404);
            }

            // Verifica se o usuário tem permissão para deletar a tarefa
            if ($task->user_id !== $user->id) {
                return response()->json([
                    "error" => [
                        "status" => "403",
                        "title" => "Forbidden",
                        "detail" => "Usuário não tem permissão para acessar o registro",
                    ]
                ], 403);
            }

            // Tenta deletar a tarefa
            if ($task->delete()) {
                return response()->json([
                    "success" => [
                        "status" => "200",
                        "title" => "OK",
                        "detail" => "Deletado com sucesso"
                    ]
                ], 200);
            }

            return response()->json([
                "error" => ["status" => "500", "title" => "Internal Server Error", "detail" => "Erro ao deletar"]
            ], 500);

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


}
