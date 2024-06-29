<?php

namespace App\Http\Controllers;

use Exception;
// use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
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

    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $taskQuery = $this->task->newQuery();

        // Verifique o papel do usuário
        if ($user->role !== 'admin') {
            // Se não for admin, o usuário pode ver apenas tasks relacionadas
            $taskQuery = $taskQuery->whereHas('assignedUsers', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        }
        
        // Selecionar colunas de usuários relacionados
        if ($request->has('assignedUsers')) {
            $atributos_assignedUsers = $request->assignedUsers;
            $taskQuery = $taskQuery->with('assignedUsers:id,' . $atributos_assignedUsers);
        } else {
            $taskQuery = $taskQuery->with('assignedUsers');
        }
        
        $taskQuery = $taskQuery->with('status:id,name');

        // Selecionar colunas do user criador da task
        if ($request->has('atributos')) {
            $atributos = $request->atributos;
            $taskQuery = $taskQuery->selectRaw($atributos)->with('user:id,name,role');
        } else {
            $taskQuery = $taskQuery->with('user:id,name,role');
        }

        // Filtra de acordo com as condições passadas por parametro de busca
        if ($request->has('filtro')) {
            $filtros = explode(';', $request->filtro);
            foreach ($filtros as $condicoes) {
                $c = explode(':', $condicoes);
                $taskQuery = $taskQuery->where($c[0], $c[1], $c[2]);
            }
        }

        $tasks = $taskQuery->paginate(10);

        return response()->json($tasks, 201);
    }


    /**
     * Display a listing of users.
     */
    public function usersAll()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $users = User::where('id', '!=', $user->id)->get();

            return response()->json([
                "success" => [
                    "status" => "201", "title" => "Created", "detail" => $users
                ]
            ], 201);
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
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $newTask = new Task();
            $newTask['user_id'] = $user->id;
            $newTask['status_id'] = 1;
            $newTask->fill($request->validated());

            if ($newTask->save()) {
                if ($request->usuariosAtribuidos) {
                    // Decodifica a string JSON para um array
                    $usuariosAtribuidos = json_decode($request->usuariosAtribuidos, true);
                    if (is_array($usuariosAtribuidos)) {
                        // Atribui os usuários à tarefa
                        $newTask->assignedUsers()->sync($usuariosAtribuidos);
                    }
                }

                return response()->json([
                    "success" => [
                        "status" => "201",
                        "title" => "Created",
                        "detail" => $newTask
                    ]
                ], 201);
            } else {
                return response()->json([
                    "error" => [
                        "status" => "500",
                        "title" => "Internal Server Error",
                        "detail" => "Erro ao salvar"
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
            $isRelatedUser = $task->assignedUsers->contains('id', $user->id);
            if ($task->user_id !== $user->id && !$isRelatedUser) {
                // if ($task->user_id !== $user->id) {
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
