<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tarea: {{ $task->title }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Detalle, adjuntos y comentarios</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Editar</a>
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900">Volver</a>
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

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-4">
                    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Detalles</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-200">
                            <div>
                                <dt class="font-semibold">Proyecto</dt>
                                <dd>{{ $task->project?->title ?? 'Sin proyecto' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold">Responsable</dt>
                                <dd>{{ $task->assignee?->name ?? 'Sin asignar' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold">Estado</dt>
                                <dd><span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900 dark:text-indigo-100">{{ ucfirst($task->status) }}</span></dd>
                            </div>
                            <div>
                                <dt class="font-semibold">Prioridad</dt>
                                <dd>{{ ucfirst($task->priority) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold">Vence</dt>
                                <dd>{{ optional($task->due_date)->format('d/m/Y') ?? 'Sin fecha' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold">Creada por</dt>
                                <dd>{{ $task->creator?->name ?? 'N/D' }}</dd>
                            </div>
                        </dl>
                        <div class="mt-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Descripción</p>
                            <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $task->description ?: 'Sin descripción' }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Comentarios</h3>
                        </div>
                        <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="space-y-3">
                            @csrf
                            <textarea name="body" rows="3" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Escribe un comentario...">{{ old('body') }}</textarea>
                            @error('body')
                                <p class="text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Comentar</button>
                            </div>
                        </form>
                        <div class="mt-4 space-y-3">
                            @forelse($task->comments as $comment)
                                <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900/60">
                                    <div class="flex justify-between">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $comment->user?->name ?? 'Usuario' }}</div>
                                        <div class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                                    </div>
                                    <p class="mt-1 text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $comment->body }}</p>
                                    @if(auth()->user()->role === 'admin' || auth()->id() === $comment->user_id)
                                        <form method="POST" action="{{ route('tasks.comments.destroy', [$task, $comment]) }}" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-300 dark:hover:text-rose-200">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-300">Sin comentarios aún.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Adjuntos</h3>
                        </div>
                        <form method="POST" action="{{ route('tasks.attachments.store', $task) }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="file" name="file" class="w-full text-sm text-gray-700 dark:text-gray-200" />
                            @error('file')
                                <p class="text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center rounded-lg border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-700 hover:border-indigo-300 hover:bg-indigo-50 dark:border-indigo-900 dark:text-indigo-100 dark:hover:border-indigo-700 dark:hover:bg-indigo-900/40">Subir archivo</button>
                            </div>
                        </form>
                        <ul class="mt-4 space-y-2 text-sm">
                            @forelse($task->attachments as $attachment)
                                <li class="flex items-center justify-between rounded-lg border border-gray-100 px-3 py-2 dark:border-gray-700">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $attachment->original_name }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($attachment->size / 1024, 1) }} KB • {{ $attachment->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('tasks.attachments.download', [$task, $attachment]) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-200 dark:hover:text-indigo-100">Descargar</a>
                                        @if(auth()->user()->role === 'admin' || auth()->id() === $attachment->user_id)
                                            <form method="POST" action="{{ route('tasks.attachments.destroy', [$task, $attachment]) }}" onsubmit="return confirm('¿Eliminar este archivo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-300 dark:hover:text-rose-200">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-300">Sin archivos adjuntos.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
