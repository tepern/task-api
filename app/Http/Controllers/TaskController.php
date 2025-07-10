<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\throwException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->input();

        if (!empty($data)) {
            if(empty($data['endTask'])) {
                $data['endTask'] = Carbon::now();
            }
            $task = new Task($data);

            $task->save();
            if(!$task) {
               return response()->json(['msg' => 'Задача не создана']);
            } else {
                return new TaskResource($task);
            }
        }
    }

    public function search(Request $request) 
    {
        $data = $request->all();
        
        $query = Task::query();
        $status = ["Новая", "В работе", "Завершена"];

        if(!empty($data['status']) && in_array($data['status'], $status)) {

            $query = $query->where('status', $data['status']);

        } else if(!empty($data['assigneeId'])) {

            $query = $query->where('assigneeId', $data['assigneeId']);

        } else $query = $query->where('status', 'В работе');

        if(!empty($data['sort']) && in_array($data['sort'], ['createdAt', 'endTask'])) {
            
            $query = $query->orderBy($data['sort']);
        }
        $tasks = $query->get()->all();

        return TaskResource::collection(
            $tasks
        );
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
        $task = Task::find($id);

        if (empty($task)) {
            return response()->json(['msg' => "Задача id=[{$id}] не найдена"]); 
        } else {
            return new TaskResource($task);
        }    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::find($id);

        if (empty($task)) {
            return response()->json(['msg' => "Задача id=[{$id}] не найдена"]); 
        }

        $data = $request->all();

        $userId = Auth::id();

        if ($task->ownerId === $userId) {
            $result = $task->update($data);

            if ($result) {
                return (new TaskResource($task))->additional(['success' => 'Задача изменена']);    
    
            } else {
                return (new TaskResource($task))->additional(['msg' => 'Ошибка сохранения']);    
            }
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        $task = Task::find($id);
        $status = ["Новая", "В работе", "Завершена"];

        if (empty($task)) {
            return response()->json(['msg' => "Задача id=[{$id}] не найдена"]); 
        }

        $data = $request->input('status');

        $userId = Auth::id();

        if (($task->ownerId === $userId || $task->assigneeId === $userId) && in_array($data, $status) ) {
            $result = $task->update($data);

            if ($result) {
                return (new TaskResource($task))->additional(['success' => 'Статус изменен']);    
    
            } else {
                return (new TaskResource($task))->additional(['msg' => 'Ошибка сохранения']);    
            }
        }
    }

    public function archive(string $id)
    {
        $task = Task::find($id);

        if (empty($task)) {
            return response()->json(['msg' => "Задача id=[{$id}] не найдена"]); 
        }

        $userId = Auth::id();

        if ($task->ownerId === $userId) {
            $task->status = 'Завершена';
            $result = $task->update();

            if ($result) {
                return (new TaskResource($task))->additional(['success' => 'Статус изменен']);    
    
            } else {
                return (new TaskResource($task))->additional(['msg' => 'Ошибка сохранения']);    
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);
        $userId = Auth::id();
        if ($task->ownerId === $userId) {
            $task->delete();
        }    
    }
}
