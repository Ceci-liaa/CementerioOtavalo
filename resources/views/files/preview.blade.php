<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista Previa del Archivo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            padding: 1.5rem;
            background-color: #f4f6f8;
            font-family: 'Segoe UI', sans-serif;
        }

        h4 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 24px;
            color:rgb(99, 88, 129); /* Título principal */
        }

        .file-name {
            color:rgb(123, 62, 150); /* Color del nombre del archivo */
        }

        .preview-frame {
            width: 100%;
            max-width: 1500px;
            height: 90vh;
            border: none;
            display: block;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .preview-image {
            display: block;
            max-width: 100%;
            max-height: 90vh;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .buttons {
            margin-top: 2rem;
            text-align: center;
        }

        .buttons a {
            text-decoration: none;
            padding: 10px 24px;
            margin: 0 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            transition: 0.3s ease;
        }

        .btn-primary {
            background-color:rgb(84, 107, 168);
            color: white;
        }

        .btn-primary:hover {
            background-color:rgb(134, 76, 130);
        }

        .btn-secondary {
            background-color:rgb(67, 80, 94);
            color: white;
        }

        .btn-secondary:hover {
            background-color:rgb(105, 125, 146);
        }

        @media (max-width: 768px) {
            .preview-frame {
                height: 75vh;
            }

            .preview-image {
                max-height: 75vh;
            }
        }
    </style>
</head>
<body>
    <h4>
        Vista Previa: 
        <span class="file-name">{{ $file->nombre_completo }}</span>
    </h4>

    @if (in_array($extension, ['pdf']))
        <iframe src="{{ $previewUrl }}" class="preview-frame"></iframe>

    @elseif (in_array($extension, ['jpg', 'jpeg', 'png']))
        <img src="{{ $previewUrl }}" alt="Vista previa de imagen" class="preview-image">

    @elseif (in_array($extension, ['doc', 'docx', 'xls', 'xlsx']))
        <iframe 
            src="https://docs.google.com/gview?url={{ urlencode($previewUrl) }}&embedded=true"
            class="preview-frame">
        </iframe>

    @else
        <div class="alert alert-warning text-center w-100">
            Este archivo no puede visualizarse en línea.<br>
            Puedes descargarlo para revisarlo localmente.
        </div>
    @endif

    <div class="buttons">
        <a href="{{ route('files.download', $file->id) }}" class="btn-primary">⬇ Descargar</a>
        <a href="{{ url()->previous() }}" class="btn-secondary">⬅ Volver</a>
    </div>
</body>
</html>
