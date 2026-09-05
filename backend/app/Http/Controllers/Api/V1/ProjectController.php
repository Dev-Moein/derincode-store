<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\PaymentServiceInterface;
use App\Contracts\Services\ProjectImageServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ReorderProjectImagesRequest;
use App\Http\Requests\Api\V1\StoreProjectImageRequest;
use App\Http\Requests\Api\V1\StoreProjectRequest;
use App\Http\Requests\Api\V1\UpdateProjectRequest;
use App\Http\Resources\Api\V1\ProjectImageResource;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\ProjectImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService,
        private readonly ProjectImageServiceInterface $imageService,
        private readonly PaymentServiceInterface $paymentService,
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

 public function show(
    string $slug
): JsonResponse {
    $project = $this->projectService->findBySlug($slug);

    if (! $project) {
        return response()->json([
            'success' => false,
            'message' => 'Project not found.',
            'data' => null,
        ], 404);
    }

    $project->load('images');

    $user = Auth::guard('sanctum')->user();

    $isPurchased = false;

    if ($user) {
        $isPurchased = $this->paymentService->hasPurchasedProject(
            $user->id,
            $project->id
        );
    }

    return response()->json([
        'success' => true,
        'message' => 'Project retrieved successfully.',
        'data' => [
            ...new ProjectResource($project)->resolve(request()),
            'is_purchased' => $isPurchased,
        ],
    ]);
}

    public function store(
        StoreProjectRequest $request
    ): JsonResponse {
        $project = $this->projectService->create(
            $request->validated(),
            $request->file('file')
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
        $project = $this->projectService->findBySlug(
            $slug
        );

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
                'data' => null,
            ], 404);
        }

        $project = $this->projectService->update(
            $project,
            $request->validated(),
            $request->file('file')
        );

        $project->load('images');

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully.',
            'data' => new ProjectResource($project),
        ]);
    }

    public function destroy(
        string $slug
    ): JsonResponse {
        $project = $this->projectService->findBySlug(
            $slug
        );

        if (! $project) {
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
        $project = $this->projectService->findBySlug(
            $slug
        );

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
                'data' => null,
            ], 404);
        }

        $images = [];

        foreach ($request->file('images') as $index => $file) {
            $images[] = $this->imageService->store(
                project: $project,
                file: $file,
                alt: $request->input('alt'),
                sortOrder: $index,
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Project images uploaded successfully.',
            'data' => ProjectImageResource::collection(
                $images
            ),
        ], 201);
    }

    public function destroyImage(
        int $image
    ): JsonResponse {
        $projectImage = ProjectImage::find($image);

        if (! $projectImage) {
            return response()->json([
                'success' => false,
                'message' => 'Project image not found.',
                'data' => null,
            ], 404);
        }

        $this->imageService->delete(
            $projectImage
        );

        return response()->json([
            'success' => true,
            'message' => 'Project image deleted successfully.',
            'data' => null,
        ]);
    }

    public function reorderImages(
        ReorderProjectImagesRequest $request,
        string $slug
    ): JsonResponse {
        $project = $this->projectService->findBySlug(
            $slug
        );

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found.',
                'data' => null,
            ], 404);
        }

        $this->imageService->reorder(
            $project,
            $request->validated('images')
        );

        $project->load([
            'images' => fn ($query) => $query
                ->orderBy('sort_order')
                ->orderBy('id'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project images reordered successfully.',
            'data' => ProjectImageResource::collection(
                $project->images
            ),
        ]);
    }
}

