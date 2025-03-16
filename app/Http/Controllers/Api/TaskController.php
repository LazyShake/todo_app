<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Tag;

class TaskController extends Controller
{
    /**
     * Получение списка задач пользователя.
     */
    public function index(Request $request)
    {
        $tasks = Task::with('tags') // Загружаем связанные теги
    ->where('user_id', auth()->id()) // Фильтруем по текущему пользователю
    ->orderBy('order') // Сортируем по полю 'order'
    ->get(); // Получаем все задачи

    return response()->json($tasks);
    }

    public function reorder(Request $request)
    {
        $taskIds = $request->task_ids; // Массив новых ID задач
    
        foreach ($taskIds as $index => $taskId) {
            $task = Task::find($taskId);
            $task->update(['order' => $index]); // Пример: добавление поля 'order' для хранения порядка
        }
    
        return response()->json(['message' => 'Задачи успешно обновлены']);
    }
    

    /**
     * Создание новой задачи.
     */
    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|min:3|max:20',
        'text' => 'max:200',
        'tags' => 'array', // Ожидаем массив тегов (как ID, так и названия)
        'tags.*' => 'exists:tags,id', // Проверка на существующие теги по ID
    ]);

    $task = Task::create([
        'title' => $request->title,
        'text' => $request->text,
        'user_id' => auth()->id(),
    ]);

    $tagIds = [];

    foreach ($request->tags as $tagValue) {
        if (is_numeric($tagValue)) {
            // Если это ID существующего тега
            $tagIds[] = (int) $tagValue;
        } else {
            // Если это новый тег, создаем его
            $tag = Tag::firstOrCreate(['title' => $tagValue]);
            $tagIds[] = $tag->id;
        }
    }

    // Привязываем теги к задаче
    $task->tags()->sync($tagIds);

    return response()->json($task->load('tags'));
}

    /**
     * Обновление задачи.
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        $request->validate([
            'title' => 'string|min:3|max:20',
            'text' => 'nullable|string|max:200',
            'tags' => 'array',
            'tags.*' => 'string|min:3|max:20'
        ]);

        $task->update($request->only(['title', 'text']));

        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagTitle) {
                $tag = Tag::firstOrCreate(['title' => $tagTitle]);
                $tagIds[] = $tag->id;
            }
            $task->tags()->sync($tagIds);
        }

        return response()->json($task->load('tags'));
    }

    /**
     * Удаление задачи.
     */
    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Задача удалена']);
    }
}
