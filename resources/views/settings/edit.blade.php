<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Configuración</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Parámetros generales y de seguridad</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-200 dark:hover:text-indigo-100">Volver al panel</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 shadow-sm dark:border-green-800 dark:bg-green-900/40 dark:text-green-100">
                    {{ session('status') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                <form method="POST" action="{{ route('settings.update') }}" class="space-y-6 p-6">
                    @csrf

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Aplicación</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre</label>
                                <input type="text" name="app_name" value="{{ old('app_name', $appName) }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                @error('app_name')
                                    <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Seguridad</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Intentos de login</label>
                                <input type="number" name="max_login_attempts" min="1" max="20" value="{{ old('max_login_attempts', $maxLoginAttempts) }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                @error('max_login_attempts')
                                    <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Archivos</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tamaño máximo (MB)</label>
                                <input type="number" name="uploads_max_size" min="1" max="200" value="{{ old('uploads_max_size', $uploadsMaxSize) }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                @error('uploads_max_size')
                                    <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Correo</h3>
                            <div class="grid gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Remitente (email)</label>
                                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $mailFromAddress) }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    @error('mail_from_address')
                                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Remitente (nombre)</label>
                                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $mailFromName) }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    @error('mail_from_name')
                                        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-700">
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">Cancelar</a>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
