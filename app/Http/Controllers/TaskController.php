<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Task::with(['project', 'assignee']);

        if (! in_array($user->role, ['admin', 'manager'], true)) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            });
        }

        foreach (['status', 'priority'] as $field) {
            if ($value = $request->string($field)->toString()) {
                $query->where($field, $value);
            }
        }

        if ($projectId = $request->integer('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($assigneeId = $request->integer('assigned_to')) {
            $query->where('assigned_to', $assigneeId);
        }

        if ($search = $request->string('q')->toString()) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($from = $request->date('due_from')) {
            $query->whereDate('due_date', '>=', $from);
        }

        if ($to = $request->date('due_to')) {
            $query->whereDate('due_date', '<=', $to);
        }

        $tasks = $query->latest()->paginate(15)->withQueryString();
        $projects = Project::orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('tasks.index', compact('tasks', 'projects', 'users'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $query = Task::with(['project', 'assignee']);

        if (! in_array($user->role, ['admin', 'manager'], true)) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            });
        }

        foreach (['status', 'priority'] as $field) {
            if ($value = $request->string($field)->toString()) {
                $query->where($field, $value);
            }
        }

        if ($projectId = $request->integer('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($assigneeId = $request->integer('assigned_to')) {
            $query->where('assigned_to', $assigneeId);
        }

        if ($search = $request->string('q')->toString()) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($from = $request->date('due_from')) {
            $query->whereDate('due_date', '>=', $from);
        }

        if ($to = $request->date('due_to')) {
            $query->whereDate('due_date', '<=', $to);
        }

        $rows = $query->orderByDesc('created_at')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Titulo', 'Estado', 'Prioridad', 'Proyecto', 'Responsable', 'Vence']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->title,
                    $row->status,
                    $row->priority,
                    $row->project?->title,
                    $row->assignee?->name,
                    optional($row->due_date)->format('Y-m-d'),
                ]);
            }
            fclose($out);
        }, 'tareas.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function create(): View
    {
        $projects = Project::orderBy('title')->get();
        $users = User::orderBy('name')->get();

        return view('tasks.create', compact('projects', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,doing,done'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $data['created_by'] = Auth::id();

        Task::create($data);

        return redirect()->route('tasks.index')->with('status', 'Tarea creada');
    }

    public function show(Task $task): View
    {
        $this->authorizeTask($task);
        $task->load(['project', 'assignee', 'creator', 'comments.user', 'attachments.user']);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        $this->authorizeTask($task);
        $projects = Project::orderBy('title')->get();
        $users = User::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,doing,done'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $task->update($data);

        return redirect()->route('tasks.index')->with('status', 'Tarea actualizada');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);
        $task->delete();

        return redirect()->route('tasks.index')->with('status', 'Tarea eliminada');
    }

    private function authorizeTask(Task $task): void
    {
        $user = Auth::user();
        if (! in_array($user->role, ['admin', 'manager'], true) && $task->created_by !== $user->id && $task->assigned_to !== $user->id) {
            abort(403);
        }
    }

    public function storeComment(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        TaskComment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        return back()->with('status', 'Comentario agregado');
    }

    public function destroyComment(Task $task, TaskComment $comment): RedirectResponse
    {
        $this->authorizeTask($task);
        if ((int) $comment->task_id !== (int) $task->id) {
            abort(404);
        }

        $user = Auth::user();
        if ($user->role !== 'admin' && $user->id !== $comment->user_id) {
            abort(403);
        }

        $comment->delete();

        return back()->with('status', 'Comentario eliminado');
    }

    public function storeAttachment(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $maxMb = (int) (\App\Models\Setting::where('key', 'uploads.max_size_mb')->first()?->value['value'] ?? 10);
        $maxKb = $maxMb * 1024;

        $data = $request->validate([
            'file' => [
                'required',
                'file',
                'max:'.$maxKb, // Laravel max is in KB
            ],
        ]);

        $path = $data['file']->store('attachments', 'public');

        TaskAttachment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'path' => $path,
            'original_name' => $data['file']->getClientOriginalName(),
            'size' => $data['file']->getSize(),
        ]);

        return back()->with('status', 'Archivo adjuntado');
    }

    public function destroyAttachment(Task $task, TaskAttachment $attachment): RedirectResponse
    {
        $this->authorizeTask($task);
        if ((int) $attachment->task_id !== (int) $task->id) {
            abort(404);
        }

        $user = Auth::user();
        if ($user->role !== 'admin' && $user->id !== $attachment->user_id) {
            abort(403);
        }

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('status', 'Adjunto eliminado');
    }

    public function downloadAttachment(Task $task, TaskAttachment $attachment)
    {
        $this->authorizeTask($task);
        if ((int) $attachment->task_id !== (int) $task->id) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($attachment->path)) {
            abort(404);
        }

        $mime = $disk->mimeType($attachment->path) ?: 'application/octet-stream';

        return response()->streamDownload(function () use ($disk, $attachment) {
            echo $disk->get($attachment->path);
        }, $attachment->original_name, [
            'Content-Type' => $mime,
        ]);
    }
}
