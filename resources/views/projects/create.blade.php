<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Nuevo proyecto</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Completa los detalles para iniciar un proyecto</p>
            </div>
            <a href="{{ route('projects.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-200 dark:hover:text-indigo-100">Volver al listado</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                <form method="POST" action="{{ route('projects.store') }}" class="space-y-6 p-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Título</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        @error('title')
                            <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Descripción</label>
                        <textarea name="description" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Estado</label>
                            <select name="status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @foreach(['draft' => 'Borrador', 'active' => 'Activo', 'archived' => 'Archivado'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Visibilidad</label>
                            <select name="visibility" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @foreach(['private' => 'Privado', 'team' => 'Equipo'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('visibility') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('visibility')
                                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Prioridad</label>
                            <select name="priority" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @foreach(['low' => 'Baja', 'medium' => 'Media', 'high' => 'Alta'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('priority') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('priority')
                                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fecha límite</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                            @error('due_date')
                                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-700">
                        <a href="{{ route('projects.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">Cancelar</a>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Guardar proyecto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
