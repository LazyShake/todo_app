<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * Получение списка тегов.
     */
    public function index()
    {
        return response()->json(Tag::all());
    }

    public function searchTags(Request $request)
    {
        $query = $request->input('query');

        $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
        
        // Ищем теги, которые содержат подстроку в названии
        $tags = Tag::where('title', 'like', '%' . $query . '%')->get();
        
        return response()->json($tags);
    }

    /**
     * 
     * Создание нового тега.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3|max:20|unique:tags'
        ]);

        $tag = Tag::create(['title' => $request->title]);

        return response()->json($tag, 201);
    }

    /**
     * Удаление тега.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['message' => 'Тег удален']);
    }
}
