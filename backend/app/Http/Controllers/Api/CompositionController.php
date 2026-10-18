<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Composition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompositionController extends Controller
{
    /**
     * Display a listing of saved compositions.
     */
    public function index(): JsonResponse
    {
        $compositions = Composition::select(['id', 'title', 'canvas_width', 'canvas_height', 'created_at', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $compositions,
        ]);
    }

    /**
     * Store a newly created composition in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'canvas_width' => 'required|integer|min:100|max:4000',
            'canvas_height' => 'required|integer|min:100|max:4000',
            'canvas_data' => 'required|array',
            'svg_preview' => 'nullable|string',
        ]);

        $composition = Composition::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Композиция успешно сохранена',
            'data' => $composition,
        ], 201);
    }

    /**
     * Display the specified composition.
     */
    public function show(int $id): JsonResponse
    {
        $composition = Composition::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $composition,
        ]);
    }

    /**
     * Update the specified composition in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $composition = Composition::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'canvas_width' => 'sometimes|integer|min:100|max:4000',
            'canvas_height' => 'sometimes|integer|min:100|max:4000',
            'canvas_data' => 'sometimes|array',
            'svg_preview' => 'nullable|string',
        ]);

        $composition->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Композиция обновлена',
            'data' => $composition,
        ]);
    }

    /**
     * Remove the specified composition from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $composition = Composition::findOrFail($id);
        $composition->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Композиция удалена',
        ]);
    }
}
