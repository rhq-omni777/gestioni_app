<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskAttachmentDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_descargar_adjunto_de_su_tarea(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $project = Project::create([
            'owner_id' => $admin->id,
            'title' => 'Proyecto Demo',
            'status' => 'active',
            'visibility' => 'team',
            'priority' => 'medium',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $admin->id,
            'assigned_to' => null,
            'title' => 'Tarea demo',
            'description' => 'Descarga de prueba',
            'status' => 'todo',
            'priority' => 'medium',
        ]);

        Storage::disk('public')->put('attachments/demo.txt', 'hola mundo');
        Storage::disk('public')->assertExists('attachments/demo.txt');

        $attachment = TaskAttachment::create([
            'task_id' => $task->id,
            'user_id' => $admin->id,
            'path' => 'attachments/demo.txt',
            'original_name' => 'demo.txt',
            'size' => strlen('hola mundo'),
        ]);

        $response = $this->actingAs($admin)->get(route('tasks.attachments.download', [$task, $attachment]));

        $response->assertOk();
        $response->assertDownload('demo.txt');
    }
}
