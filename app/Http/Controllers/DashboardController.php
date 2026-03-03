<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'manager'], true)) {
            $data = [
                'totalUsers' => User::count(),
                'activeProjects' => Project::where('status', 'active')->count(),
                'pendingTasks' => Task::where('status', 'todo')->count(),
                'completedTasks' => Task::where('status', 'done')->count(),
            ];
        } else {
            $data = [
                'totalUsers' => null,
                'activeProjects' => Project::where('owner_id', $user->id)->count(),
                'pendingTasks' => Task::where('assigned_to', $user->id)->where('status', 'todo')->count(),
                'completedTasks' => Task::where('assigned_to', $user->id)->where('status', 'done')->count(),
            ];
        }

        return view('dashboard', $data);
    }
}
