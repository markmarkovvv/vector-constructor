<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Illustration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IllustrationController extends Controller
{
    /**
     * Display a listing of illustrations.
     */
    public function index(): JsonResponse
    {
        $illustrations = Illustration::select(['id', 'title', 'category', 'description', 'view_box', 'svg_content', 'created_at'])
            ->withCount('components')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $illustrations,
        ]);
    }

    /**
     * Display the specified illustration with full SVG content.
     */
    public function show(int $id): JsonResponse
    {
        $illustration = Illustration::with('components')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $illustration,
        ]);
    }

    /**
     * Upload / store a new SVG illustration.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'svg_content' => 'required|string',
            'view_box' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $svgContent = $validated['svg_content'];
        $viewBox = $validated['view_box'] ?? null;

        if (!$viewBox && preg_match('/viewBox\s*=\s*["\']([^"\']+)["\']/i', $svgContent, $matches)) {
            $viewBox = $matches[1];
        }

        $illustration = Illustration::create([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? 'Пользовательские',
            'svg_content' => $svgContent,
            'view_box' => $viewBox ?? '0 0 800 600',
            'description' => $validated['description'] ?? 'Пользовательская загруженная SVG-иллюстрация',
        ]);

        $illustration->components_count = 0;

        return response()->json([
            'status' => 'success',
            'message' => 'Иллюстрация успешно сохранена',
            'data' => $illustration,
        ], 201);
    }
}
