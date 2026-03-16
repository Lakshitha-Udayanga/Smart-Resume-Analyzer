<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class JobImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_import_jobs_from_excel()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a fake Excel file
        // For simplicity in this test, we won't actually create a valid Excel binary,
        // but we'll test the controller's ability to receive a file and call the import facade.
        
        Excel::fake();

        $file = UploadedFile::fake()->create('jobs.xlsx');

        $response = $this->post(route('jobs.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('jobs.index'));
        $response->assertSessionHas('success', 'Jobs imported successfully.');

        Excel::assertImported('jobs.xlsx');
    }

    public function test_import_requires_a_file()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('jobs.import'), [
            'file' => null,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_import_validates_file_extension()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('jobs.txt');

        $response = $this->post(route('jobs.import'), [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }
}
