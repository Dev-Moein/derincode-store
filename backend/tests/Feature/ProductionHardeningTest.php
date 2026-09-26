<?php

namespace Tests\Feature\Services;

use App\Contracts\Services\PaymentGatewayInterface;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\ProjectFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ProductionHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_file_replacement_keeps_database_and_storage_consistent(): void
    {
        Storage::fake('local');

        $project = Project::factory()->create([
            'file_path' => 'projects/files/old.zip',
            'file_name' => 'old.zip',
            'file_size' => 3,
        ]);

        Storage::disk('local')->put(
            'projects/files/old.zip',
            'old'
        );

        $file = UploadedFile::fake()->create(
            'new.zip',
            10,
            'application/zip'
        );

        $updated = app(ProjectFileService::class)->replace(
            $project,
            $file
        );

        $this->assertNotSame(
            'projects/files/old.zip',
            $updated->file_path
        );

        $this->assertSame(
            'new.zip',
            $updated->file_name
        );

        Storage::disk('local')->assertMissing(
            'projects/files/old.zip'
        );

        Storage::disk('local')->assertExists(
            $updated->file_path
        );

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'file_path' => $updated->file_path,
            'file_name' => 'new.zip',
        ]);
    }

    public function test_project_file_replacement_rejects_non_zip_before_touching_old_file(): void
    {
        Storage::fake('local');

        $project = Project::factory()->create([
            'file_path' => 'projects/files/old.zip',
            'file_name' => 'old.zip',
            'file_size' => 3,
        ]);

        Storage::disk('local')->put(
            'projects/files/old.zip',
            'old'
        );

        $invalidFile = UploadedFile::fake()->create(
            'malware.exe',
            10,
            'application/octet-stream'
        );

        try {
            app(ProjectFileService::class)->replace(
                $project,
                $invalidFile
            );

            $this->fail('Expected invalid ZIP validation to throw.');
        } catch (\RuntimeException $exception) {
            $this->assertSame(
                'Only valid ZIP files are allowed.',
                $exception->getMessage()
            );
        }

        Storage::disk('local')->assertExists(
            'projects/files/old.zip'
        );
    }

    public function test_pending_payment_with_authority_reuses_existing_gateway_session(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'price' => 1500,
            'currency' => 'USD',
            'is_for_sale' => true,
            'status' => 'published',
        ]);

        $payment = Payment::factory()->pending()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'gateway' => 'zarinpal',
            'authority' => 'EXISTING-AUTHORITY',
        ]);

        $gateway = Mockery::mock(PaymentGatewayInterface::class);

        $gateway->shouldReceive('paymentUrl')
            ->once()
            ->with(Mockery::on(
                fn (Payment $model) =>
                    $model->id === $payment->id
                    && $model->authority === 'EXISTING-AUTHORITY'
            ))
            ->andReturn(
                'https://sandbox.zarinpal.com/pg/StartPay/EXISTING-AUTHORITY'
            );

        $gateway->shouldNotReceive('request');

        $service = new PaymentService(
            app(\App\Contracts\Repositories\PaymentRepositoryInterface::class),
            $gateway,
        );

        $result = $service->initiatePayment(
            $payment,
            'https://example.com/callback'
        );

        $this->assertTrue($result['success']);
        $this->assertSame(
            'EXISTING-AUTHORITY',
            $result['authority']
        );
        $this->assertSame(
            'https://sandbox.zarinpal.com/pg/StartPay/EXISTING-AUTHORITY',
            $result['payment_url']
        );
    }
}
