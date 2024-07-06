<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Services\ApiServices;
use App\Models\TaskAssignments;

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
            $taskQuery->whereHas('assignedUsers', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        } else {
            // Se for admin, pode ver todas as tasks que criou e as tasks relacionadas
            $taskQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('assignedUsers', function ($subQuery) use ($user) {
                        $subQuery->where('users.id', $user->id);
                    });
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
                $taskQuery = $taskQuery->where($c[0], $c[1],  '%' . $c[2] . '%');
            }
        }

        $tasks = $taskQuery->paginate(10);

        return ApiServices::statusCodeWithoutHeader($tasks, 201);
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
                return ApiServices::statusCode201($newTask);
            } else {
                return ApiServices::statusCode500("Erro ao salvar");
            }
        } catch (Exception $e) {
            return ApiServices::statusCode500($e->getMessage());
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


            // Verifica se a tarefa existe
            if ($task === null) {
                return ApiServices::statusCode404("Registro não encontrado");
            }

            // Verifica se o usuário tem permissão para atualizar a tarefa
            $isRelatedUser = $task->assignedUsers->contains('id', $user->id);
            if ($task->user_id !== $user->id && !$isRelatedUser) {
                return ApiServices::statusCode403("Usuário não tem permissão para acessar o registro");
            }

            if (gettype($request->usuariosAtribuidos) != "NULL") {
                // Obtém os IDs de usuários atribuídos do request e converte para array de inteiros
                $atribuidos = explode(',', $request->usuariosAtribuidos);
                $atribuidos = array_map('intval', $atribuidos);

                // Obtém os registros atuais na tabela task_assignments para a task em questão
                $currentAssignments = TaskAssignments::where('task_id', $task->id)
                    ->pluck('user_id')
                    ->toArray(); // 
                $toRemove = array_diff($currentAssignments, $atribuidos);

                // // Remove os registros que estão na tabela mas não estão na lista de atribuídos
                if (!empty($toRemove)) {
                    TaskAssignments::where('task_id', $task->id)
                        ->whereIn('user_id', $toRemove)
                        ->delete();
                }

                // Adiciona novos registros para os atribuídos que não estão na tabela
                $toAdd = array_diff($atribuidos, $currentAssignments);
                foreach ($toAdd as $userId) {
                    $taskAssignment = new TaskAssignments();
                    $taskAssignment->task_id = $task->id;
                    $taskAssignment->user_id = $userId;
                    $taskAssignment->save();
                }
            } else {
                TaskAssignments::where('task_id', $task->id)->delete();
            }

            if ($task->update($request->all())) {
                return ApiServices::statusCode200("Atualizado com sucesso");
            }

            return ApiServices::statusCode500("Erro ao atualizar");
        } catch (Exception $e) {
            return ApiServices::statusCode500($e->getMessage());
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
                return ApiServices::statusCode404("Registro não encontrado");
            }

            // Verifica se o usuário tem permissão para deletar a tarefa
            if ($task->user_id !== $user->id) {
                return ApiServices::statusCode403("Usuário não tem permissão para acessar o registro");
            }
            
            // Tenta deletar a tarefa
            if ($task->delete()) {
                return ApiServices::statusCode200("Deletado com sucesso");
            }
            
            return ApiServices::statusCode500("Erro ao deletar");
        } catch (Exception $e) {
            return ApiServices::statusCode500($e->getMessage());
        }
    }
}
