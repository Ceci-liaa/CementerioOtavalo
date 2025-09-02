<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Detalles del Archivo</strong>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item"><strong>ID:</strong> {{ $file->id }}</li>
                                <li class="list-group-item"><strong>Nombre Original:</strong> {{ $file->name_original }}</li>
                                <li class="list-group-item"><strong>Nombre completo:</strong> {{ $file->nombre_completo }}</li>                                
                                {{-- Mostrar la ubicación en formato "Hola\Estudios\Textil" --}}
                                <li class="list-group-item"><strong>Ubicación:</strong> 
                                    {{ $file->folder ? implode('\\', $file->folder->getAncestors()->pluck('name')->toArray()) . '\\' . $file->folder->name : 'Raíz' }}
                                </li>

                                <li class="list-group-item"><strong>Subido por:</strong> {{ $file->user->name }}</li>
                                <li class="list-group-item"><strong>Fecha de subida:</strong> {{ $file->created_at }}</li>
                                <li class="list-group-item">
                                    <strong>Descargar Archivo:</strong> 
                                    <a href="{{ route('files.download', $file->id) }}" class="text-decoration-none text-primary">
                                        📥 Descargar {{ $file->nombre_completo }}.{{ strtolower($file->type) }}
                                    </a>
                                </li>
                            </ul>

                            <a href="{{ request('from') === 'explorer' ? route('folders.explorer', $file->folder_id) : route('files.index') }}" 
                               class="btn btn-secondary">⬅ Volver</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-app.footer />
    </main>
</x-app-layout>
