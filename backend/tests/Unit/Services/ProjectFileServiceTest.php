<?php

namespace Tests\Unit\Services;

use App\Models\Project;
use App\Services\ProjectFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ProjectFileServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProjectFileService $projectFileService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->projectFileService = new ProjectFileService;
    }

    /**
     * It can store a ZIP project file.
     */
    public function test_it_can_store_project_file(): void
    {
        $project = Project::factory()->create();

        $file = UploadedFile::fake()->create(
            'portfolio.zip',
            1024,
            'application/zip'
        );

        $result = $this->projectFileService->store(
            $project,
            $file
        );

        $this->assertNotNull(
            $result->file_path
        );

        $this->assertSame(
            'portfolio.zip',
            $result->file_name
        );

        $this->assertSame(
            $file->getSize(),
            $result->file_size
        );

        $this->assertStringStartsWith(
            'projects/files/',
            $result->file_path
        );

        $this->assertStringEndsWith(
            '.zip',
            $result->file_path
        );

        Storage::disk('local')->assertExists(
            $result->file_path
        );
    }

    /**
     * It rejects invalid uploaded files.
     */
    public function test_it_rejects_invalid_uploaded_file(): void
    {
        $project = Project::factory()->create();

        $tempFile = tempnam(sys_get_temp_dir(), 'test');

        file_put_contents($tempFile, 'invalid upload');

        $file = new UploadedFile(
            $tempFile,
            'project.zip',
            'application/zip',
            UPLOAD_ERR_CANT_WRITE,
            true
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'The uploaded project file is invalid.'
        );

        $this->projectFileService->store(
            $project,
            $file
        );
    }

    /**
     * It rejects non ZIP files.
     */
    public function test_it_rejects_non_zip_files(): void
    {
        $project = Project::factory()->create();

        $file = UploadedFile::fake()->create(
            'document.pdf',
            100,
            'application/pdf'
        );

        $this->expectException(
            RuntimeException::class
        );

        $this->expectExceptionMessage(
            'Only valid ZIP files are allowed.'
        );

        $this->projectFileService->store(
            $project,
            $file
        );
    }

    /**
     * It can replace an existing project file.
     */
    public function test_it_can_replace_project_file(): void
    {
        $project = Project::factory()->create([
            'file_path' => 'projects/files/old-file.zip',
            'file_name' => 'old-file.zip',
            'file_size' => 100,
        ]);

        Storage::disk('local')->put(
            $project->file_path,
            'old file content'
        );

        $oldPath = $project->file_path;

        $newFile = UploadedFile::fake()->create(
            'new-project.zip',
            200,
            'application/zip'
        );

        $result = $this->projectFileService->replace(
            $project,
            $newFile
        );

        Storage::disk('local')->assertMissing(
            $oldPath
        );

        Storage::disk('local')->assertExists(
            $result->file_path
        );

        $this->assertNotSame(
            $oldPath,
            $result->file_path
        );

        $this->assertSame(
            'new-project.zip',
            $result->file_name
        );

        $this->assertSame(
            $newFile->getSize(),
            $result->file_size
        );
    }

    /**
     * It can delete a project file.
     */
    public function test_it_can_delete_project_file(): void
    {
        $project = Project::factory()->create([
            'file_path' => 'projects/files/project.zip',
            'file_name' => 'project.zip',
            'file_size' => 500,
        ]);

        Storage::disk('local')->put(
            $project->file_path,
            'project content'
        );

        $path = $project->file_path;

        $result = $this->projectFileService->delete(
            $project
        );

        $this->assertTrue(
            $result
        );

        Storage::disk('local')->assertMissing(
            $path
        );

        $project->refresh();

        $this->assertNull(
            $project->file_path
        );

        $this->assertNull(
            $project->file_name
        );

        $this->assertNull(
            $project->file_size
        );
    }

    /**
     * Deleting a project without a file still succeeds.
     */
    public function test_it_can_delete_project_without_file(): void
    {
        $project = Project::factory()->create([
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
        ]);

        $result = $this->projectFileService->delete(
            $project
        );

        $this->assertTrue(
            $result
        );

        $project->refresh();

        $this->assertNull(
            $project->file_path
        );

        $this->assertNull(
            $project->file_name
        );

        $this->assertNull(
            $project->file_size
        );
    }

    /**
     * It returns true when project file exists.
     */
    public function test_it_returns_true_when_project_file_exists(): void
    {
        $project = Project::factory()->create([
            'file_path' => 'projects/files/project.zip',
        ]);

        Storage::disk('local')->put(
            $project->file_path,
            'project content'
        );

        $result = $this->projectFileService->exists(
            $project
        );

        $this->assertTrue(
            $result
        );
    }

    /**
     * It returns false when project has no file path.
     */
    public function test_it_returns_false_when_project_has_no_file_path(): void
    {
        $project = Project::factory()->create([
            'file_path' => null,
        ]);

        $result = $this->projectFileService->exists(
            $project
        );

        $this->assertFalse(
            $result
        );
    }

    /**
     * It returns false when physical project file does not exist.
     */
    public function test_it_returns_false_when_project_file_is_missing(): void
    {
        $project = Project::factory()->create([
            'file_path' => 'projects/files/missing.zip',
        ]);

        $result = $this->projectFileService->exists(
            $project
        );

        $this->assertFalse(
            $result
        );
    }

    /**
     * It can return the physical path of an existing project file.
     */
    public function test_it_can_get_project_file_path(): void
    {
        $project = Project::factory()->create([
            'file_path' => 'projects/files/project.zip',
        ]);

        Storage::disk('local')->put(
            $project->file_path,
            'project content'
        );

        $result = $this->projectFileService->getPath(
            $project
        );

        $this->assertNotNull(
            $result
        );

        $this->assertStringEndsWith(
            'projects/files/project.zip',
            str_replace(
                '\\',
                '/',
                $result
            )
        );
    }

    /**
     * It returns null when project has no file path.
     */
    public function test_it_returns_null_path_when_project_has_no_file(): void
    {
        $project = Project::factory()->create([
            'file_path' => null,
        ]);

        $result = $this->projectFileService->getPath(
            $project
        );

        $this->assertNull(
            $result
        );
    }

    /**
     * It returns null path when physical file is missing.
     */
    public function test_it_returns_null_path_when_physical_file_is_missing(): void
    {
        $project = Project::factory()->create([
            'file_path' => 'projects/files/missing.zip',
        ]);

        $result = $this->projectFileService->getPath(
            $project
        );

        $this->assertNull(
            $result
        );
    }
}
