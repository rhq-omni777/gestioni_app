<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Project::query();

        if (! in_array($user->role, ['admin', 'manager'], true)) {
            $query->where('owner_id', $user->id);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($priority = $request->string('priority')->toString()) {
            $query->where('priority', $priority);
        }

        if ($visibility = $request->string('visibility')->toString()) {
            $query->where('visibility', $visibility);
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

        $projects = $query->latest()->paginate(15)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $query = Project::query();

        if (! in_array($user->role, ['admin', 'manager'], true)) {
            $query->where('owner_id', $user->id);
        }

        foreach (['status', 'priority', 'visibility'] as $field) {
            if ($value = $request->string($field)->toString()) {
                $query->where($field, $value);
            }
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

        $rows = $query->orderByDesc('created_at')->get(['title', 'status', 'priority', 'visibility', 'due_date']);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Titulo', 'Estado', 'Prioridad', 'Visibilidad', 'Vence']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->title,
                    $row->status,
                    $row->priority,
                    $row->visibility,
                    optional($row->due_date)->format('Y-m-d'),
                ]);
            }
            fclose($out);
        }, 'proyectos.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,active,archived'],
            'visibility' => ['required', 'in:private,team'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        $data['owner_id'] = Auth::id();

        Project::create($data);

        return redirect()->route('projects.index')->with('status', 'Proyecto creado');
    }

    public function edit(Project $project): View
    {
        $this->authorizeProject($project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,active,archived'],
            'visibility' => ['required', 'in:private,team'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        $project->update($data);

        return redirect()->route('projects.index')->with('status', 'Proyecto actualizado');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorizeProject($project);
        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Proyecto eliminado');
    }

    private function authorizeProject(Project $project): void
    {
        $user = Auth::user();
        if (! in_array($user->role, ['admin', 'manager'], true) && $project->owner_id !== $user->id) {
            abort(403);
        }
    }
}
