<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\SearchTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task\Task;
use App\Models\Task\TaskStatusEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function create(CreateTaskRequest $request)
    {
        $data = $request->input();

        if (!empty($data)) {
            if (empty($data['endTask'])) {
                $data['endTask'] = Carbon::now();
            }
            $data['ownerId'] = Auth::id();
            $task = new Task($data);

            $task->save();
            if (!$task) {
                return response()->json(['msg' => 'Задача не создана']);
            } else {
                return new TaskResource($task);
            }
        }
    }

    public function search(SearchTaskRequest $request)
    {
        $data = $request->all();

        $query = Task::query();
        $status = [TaskStatusEnum::NEW->value, TaskStatusEnum::ACTIVE->value, TaskStatusEnum::COMPLETED->value];

        if (!empty($data['status']) && in_array($data['status'], $status)) {

            $query = $query->where('status', $data['status']);
        } else $query = $query->where('status', TaskStatusEnum::ACTIVE->value);

        if (!empty($data['assigneeId'])) {

            $query = $query->where('assigneeId', $data['assigneeId']);
        }

        if (!empty($data['sort']) && in_array($data['sort'], ['createdAt', 'endTask'])) {

            $query = $query->orderBy($data['sort']);
        }
        $tasks = $query->get()->all();

        return TaskResource::collection(
            $tasks
        );
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
    public function update(CreateTaskRequest $request, string $id)
    {
        /** @var Task $task */
        $task = Task::find($id);

        if (empty($task)) {
            return response()->json(['msg' => "Задача id=[{$id}] не найдена"]);
        }

        $data = $request->all();
        if ($data['status'] === TaskStatusEnum::COMPLETED->value) {
            $data['finished_at'] = Carbon::now();
        }

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
        /** @var Task $task */
        $task = Task::find($id);
        $status = [TaskStatusEnum::NEW->value, TaskStatusEnum::ACTIVE->value, TaskStatusEnum::COMPLETED->value];

        if (empty($task)) {
            return response()->json(['msg' => "Задача id=[{$id}] не найдена"]);
        }

        $data = $request->input('status');

        $userId = Auth::id();

        if (($task->ownerId === $userId || $task->assigneeId === $userId) && in_array($data, $status)) {
            $result = $task->update($data);

            if ($result) {
                if ($task->status = TaskStatusEnum::COMPLETED->value) {
                    $task->finished_at = Carbon::now();
                    $task->save();
                }

                return (new TaskResource($task))->additional(['success' => 'Статус изменен']);
            } else {
                return (new TaskResource($task))->additional(['msg' => 'Ошибка сохранения']);
            }
        }
    }

    public function archive(string $id)
    {
        /** @var Task $task */
        $task = Task::find($id);

        if (empty($task)) {
            return response()->json(['msg' => "Задача id=[{$id}] не найдена"]);
        }

        $userId = Auth::id();

        if ($task->ownerId === $userId) {
            $task->status = TaskStatusEnum::COMPLETED->value;
            $task->finished_at = Carbon::now();
            $result = $task->save();

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

        if (empty($task)) {
            return response()->json(['msg' => "Задача id=[{$id}] не найдена"]);
        }

        $userId = Auth::id();
        if ($task->ownerId === $userId) {
            $task->delete();
        }
    }
}
