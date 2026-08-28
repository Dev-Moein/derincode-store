<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\DownloadServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DownloadResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    public function __construct(
        private readonly DownloadServiceInterface $downloadService,
    ) {}

    public function index(Request $request)
    {
        $downloads = $this->downloadService->paginateForUser(
            $request->user()->id,
            $request->only([
                'project_id',
                'per_page',
            ])
        );

        return DownloadResource::collection($downloads);
    }

    public function download(
        Request $request,
        int $projectId
    ): BinaryFileResponse {
        return $this->downloadService->download(
            $request->user(),
            $projectId
        );
    }
}
