<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    /**
     * Display a listing of extracted components.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Component::with('illustration:id,title');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('illustration_id')) {
            $query->where('illustration_id', $request->illustration_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $components = $query->orderBy('id', 'desc')->get();

        // Get unique categories list for filters
        $categories = Component::select('category')->distinct()->pluck('category');

        return response()->json([
            'status' => 'success',
            'data' => $components,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created extracted component in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'svg_content' => 'required|string',
            'view_box' => 'required|string',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'illustration_id' => 'nullable|exists:illustrations,id',
            'tags' => 'nullable|array',
        ]);

        $component = Component::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'svg_content' => $validated['svg_content'],
            'view_box' => $validated['view_box'],
            'width' => $validated['width'] ?? 150,
            'height' => $validated['height'] ?? 150,
            'illustration_id' => $validated['illustration_id'] ?? null,
            'tags' => $validated['tags'] ?? [],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Компонент успешно извлечён и сохранён в библиотеку',
            'data' => $component->load('illustration:id,title'),
        ], 201);
    }

    /**
     * Display the specified component.
     */
    public function show(int $id): JsonResponse
    {
        $component = Component::with('illustration:id,title')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $component,
        ]);
    }

    /**
     * Remove the specified component from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $component = Component::findOrFail($id);
        $component->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Компонент удалён из библиотеки',
        ], 200);
    }
}
