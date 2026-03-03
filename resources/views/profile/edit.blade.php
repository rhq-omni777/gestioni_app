<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Perfil y seguridad</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Datos de cuenta, contraseña y estado</p>
            </div>
            <div class="rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-100">
                Rol: {{ strtoupper(auth()->user()->role) }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Estado de cuenta</p>
                    <div class="mt-2 inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold {{ auth()->user()->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100' : 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-100' }}">
                        {{ auth()->user()->is_active ? 'Activa' : 'Desactivada' }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Verificación de correo</p>
                    <div class="mt-2 inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold {{ auth()->user()->hasVerifiedEmail() ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100' : 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-100' }}">
                        {{ auth()->user()->hasVerifiedEmail() ? 'Correo verificado' : 'Pendiente de verificación' }}
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
