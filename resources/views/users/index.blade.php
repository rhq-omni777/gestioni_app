<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Usuarios</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Administración de roles y estado</p>
            </div>
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
                    <form method="GET" action="{{ route('users.index') }}" class="grid gap-3 md:grid-cols-4">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar nombre o correo" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        <select name="role" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Rol</option>
                            @foreach(['admin'=>'Admin','manager'=>'Manager','user'=>'Usuario'] as $value=>$label)
                                <option value="{{ $value }}" @selected(request('role')===$value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="active" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Estado</option>
                            <option value="1" @selected(request('active')==='1')>Activo</option>
                            <option value="0" @selected(request('active')==='0')>Inactivo</option>
                        </select>
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900">Limpiar</a>
                            <button class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700" type="submit">Filtrar</button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Correo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Rol</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900 dark:text-indigo-100">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100' : 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-100' }}">
                                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end">
                                            <form method="POST" action="{{ route('users.update', $user) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" @disabled($user->id === auth()->id())>
                                                    @foreach(['admin'=>'Admin','manager'=>'Manager','user'=>'Usuario'] as $value=>$label)
                                                        <option value="{{ $value }}" @selected($user->role === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                                    <input type="checkbox" name="is_active" value="1" @checked($user->is_active) @disabled($user->id === auth()->id()) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900" />
                                                    Activo
                                                </label>
                                                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-indigo-700">Guardar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-300">Sin usuarios</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
