<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Proyectos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gestiona tus iniciativas y fechas clave</p>
            </div>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Nuevo proyecto</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 shadow-sm dark:border-green-800 dark:bg-green-900/40 dark:text-green-100">
                    {{ session('status') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                <div class="border-b border-gray-100 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
                    <form method="GET" action="{{ route('projects.index') }}" class="grid gap-3 md:grid-cols-5">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar título" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        <select name="status" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Estado</option>
                            @foreach(['draft' => 'Borrador', 'active' => 'Activo', 'archived' => 'Archivado'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="priority" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Prioridad</option>
                            @foreach(['low'=>'Baja','medium'=>'Media','high'=>'Alta'] as $value=>$label)
                                <option value="{{ $value }}" @selected(request('priority')===$value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2">
                            <input type="date" name="due_from" value="{{ request('due_from') }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                            <input type="date" name="due_to" value="{{ request('due_to') }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div class="flex gap-2 justify-end md:justify-start">
                            <a href="{{ route('projects.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900">Limpiar</a>
                            <button class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700" type="submit">Filtrar</button>
                            <a href="{{ route('projects.export', request()->query()) }}" class="inline-flex items-center rounded-lg border border-emerald-200 px-3 py-2 text-sm font-semibold text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50 dark:border-emerald-900 dark:text-emerald-100 dark:hover:border-emerald-700 dark:hover:bg-emerald-900/40">Exportar CSV</a>
                        </div>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Título</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Prioridad</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Visibilidad</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Vence</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @forelse($projects as $project)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $project->title }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900 dark:text-indigo-100">{{ ucfirst($project->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ ucfirst($project->priority) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ ucfirst($project->visibility) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ optional($project->due_date)->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-3 text-sm">
                                            <a href="{{ route('projects.edit', $project) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-200 dark:hover:text-indigo-100">Editar</a>
                                            <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('¿Eliminar este proyecto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-300 dark:hover:text-rose-200">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-300">Aún no hay proyectos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
