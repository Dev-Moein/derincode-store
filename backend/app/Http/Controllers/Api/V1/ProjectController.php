<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\ProjectImageServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProjectImageRequest;
use App\Http\Requests\Api\V1\StoreProjectRequest;
use App\Http\Requests\Api\V1\UpdateProjectRequest;
use App\Http\Resources\Api\V1\ProjectImageResource;
use App\Http\Resources\Api\V1\ProjectResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService,
        private readonly ProjectImageServiceInterface $imageService,
    ) {}

    public function index(Request $request)
    {
        $projects = $this->projectService->paginate(
            $request->only([
                'search',
                'status',
                'is_featured',
                'is_for_sale',
                'per_page',
            ])
        );

        return ProjectResource::collection($projects);
    }

    public function show(string $slug): JsonResponse
    {
        $project = $this->projectService->findBySlug($slug);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Project retrieved successfully.',
            'data' => new ProjectResource($project),
        ]);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->create(
            $request->validated()
        );

        $project->load('images');

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully.',
            'data' => new ProjectResource($project),
        ], 201);
    }

    public function update(
        UpdateProjectRequest $request,
        string $slug
    ): JsonResponse {
        $project = $this->projectService->findBySlug($slug);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
                'data' => null,
            ], 404);
        }

        $project = $this->projectService->update(
            $project,
            $request->validated()
        );

        $project->load('images');

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully.',
            'data' => new ProjectResource($project),
        ]);
    }

    public function destroy(string $slug): JsonResponse
    {
        $project = $this->projectService->findBySlug($slug);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
                'data' => null,
            ], 404);
        }

        $this->projectService->delete($project);

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully.',
            'data' => null,
        ]);
    }

    public function storeImage(
        StoreProjectImageRequest $request,
        string $slug
    ): JsonResponse {
        $project = $this->projectService->findBySlug($slug);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
                'data' => null,
            ], 404);
        }

        $image = $this->imageService->store(
            project: $project,
            file: $request->file('image'),
            alt: $request->input('alt'),
            sortOrder: (int) $request->input('sort_order', 0),
        );

        return response()->json([
            'success' => true,
            'message' => 'Project image uploaded successfully.',
            'data' => new ProjectImageResource($image),
        ], 201);
    }

    public function destroyImage(int $image): JsonResponse
    {
        $projectImage = \App\Models\ProjectImage::find($image);

        if (!$projectImage) {
            return response()->json([
                'success' => false,
                'message' => 'Project image not found.',
                'data' => null,
            ], 404);
        }

        $this->imageService->delete($projectImage);

        return response()->json([
            'success' => true,
            'message' => 'Project image deleted successfully.',
            'data' => null,
        ]);
    }
}
