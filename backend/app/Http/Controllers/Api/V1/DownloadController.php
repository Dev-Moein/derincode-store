<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\DownloadServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    public function __construct(
        private readonly DownloadServiceInterface $downloadService,
    ) {}

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
