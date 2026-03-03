<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Panel
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Resumen rápido de la operación</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('projects.index') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                    Ver proyectos
                </a>
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center rounded-lg border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:border-indigo-300 hover:bg-indigo-50 dark:border-indigo-900 dark:text-indigo-100 dark:hover:border-indigo-700 dark:hover:bg-indigo-900/40">
                    Ver tareas
                </a>
                @if(in_array(auth()->user()->role, ['admin','manager']))
                    <a href="{{ route('users.index') }}" class="inline-flex items-center rounded-lg border border-sky-200 px-4 py-2 text-sm font-semibold text-sky-700 hover:border-sky-300 hover:bg-sky-50 dark:border-sky-900 dark:text-sky-100 dark:hover:border-sky-700 dark:hover:bg-sky-900/40">
                        Usuarios
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @if($totalUsers !== null)
                    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Usuarios</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $totalUsers }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Registrados en la plataforma</p>
                    </div>
                @endif
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Proyectos activos</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $activeProjects }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">En curso actualmente</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tareas pendientes</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $pendingTasks }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Estado: por hacer</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tareas completadas</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $completedTasks }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Últimas finalizadas</p>
                </div>
            </div>

            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                <div class="flex flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Accesos rápidos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Crea proyectos y tareas en segundos.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('projects.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                            Nuevo proyecto
                        </a>
                        <a href="{{ route('tasks.create') }}" class="inline-flex items-center rounded-lg border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:border-indigo-300 hover:bg-indigo-50 dark:border-indigo-900 dark:text-indigo-100 dark:hover:border-indigo-700 dark:hover:bg-indigo-900/40">
                            Nueva tarea
                        </a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('settings.edit') }}" class="inline-flex items-center rounded-lg border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-700 hover:border-amber-300 hover:bg-amber-50 dark:border-amber-900 dark:text-amber-100 dark:hover:border-amber-700 dark:hover:bg-amber-900/40">
                                Configuración
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
