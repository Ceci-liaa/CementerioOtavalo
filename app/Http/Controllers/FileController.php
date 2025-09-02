<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileName;
use App\Models\Folder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;



class FileController extends Controller
{
    public function index()
    {
        // Ordenar archivos por nombre compuesto (prefijo + nombre predefinido + sufijo)
        $files = File::with(['file_name', 'user'])
            ->leftJoin('file_names', 'files.file_name_id', '=', 'file_names.id')
            ->orderByRaw("
        CASE 
            WHEN (COALESCE(prefix, '') || ' ' || COALESCE(file_names.name, '') || ' ' || COALESCE(suffix, '')) ~ '^[0-9]+\\.-' THEN 0
            ELSE 1
        END,
        CASE 
            WHEN (COALESCE(prefix, '') || ' ' || COALESCE(file_names.name, '') || ' ' || COALESCE(suffix, '')) ~ '^[0-9]+\\.-' THEN 
                CAST(
                    regexp_replace(
                        (COALESCE(prefix, '') || ' ' || COALESCE(file_names.name, '') || ' ' || COALESCE(suffix, '')),
                        '^([0-9]+)\\..*',
                        '\\1'
                    ) AS INTEGER
                )
            ELSE NULL
        END,
        LOWER(COALESCE(prefix, '') || ' ' || COALESCE(file_names.name, '') || ' ' || COALESCE(suffix, ''))
    ")
            ->select('files.*')
            ->paginate(10);


        return view('files.index', compact('files'));
    }

    public function create(Request $request)
    {
        $currentFolderId = $request->input('folder_id', null);
        $currentFolder = Folder::find($currentFolderId);

        // Obtener subcarpetas de la carpeta actual
        $folders = Folder::where('parent_id', $currentFolderId)
            ->orderByRaw("
            CASE 
                WHEN name ~ '^[0-9]+\\.-' THEN 0
                ELSE 1
            END,
            CASE 
                WHEN name ~ '^[0-9]+\\.-' THEN (string_to_array(name, '.-'))[1]::INTEGER
                ELSE NULL
            END,
            name
        ")
            ->get();
        $fileNames = FileName::all();

        // Asegurar que $breadcrumb sea una colección de Laravel
        $breadcrumb = collect();
        if ($currentFolder) {
            $breadcrumb = collect($currentFolder->getAncestors())->map(function ($folder) {
                return ['id' => $folder->id, 'name' => $folder->name];
            });

            $breadcrumb->push(['id' => $currentFolder->id, 'name' => $currentFolder->name]);
        }

        return view('files.create', compact('folders', 'fileNames', 'breadcrumb', 'currentFolderId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'uploaded_file' => 'required|file',
            'file_name_id' => 'required|exists:file_names,id',
            'folder_id' => 'required|exists:folders,id',
        ], [
            'uploaded_file.required' => '⚠️ Debes subir un archivo.',
            'file_name_id.required' => '⚠️ Debes seleccionar un nombre predefinido.',
            'file_name_id.exists' => '⚠️ El nombre predefinido no es válido.',
            'folder_id.required' => '⚠️ Debes seleccionar una carpeta antes de subir el archivo.',
            'folder_id.exists' => '⚠️ La carpeta seleccionada no existe.',
        ]);

        $prefix = trim($request->input('prefix'));
        $suffix = trim($request->input('suffix'));
        $baseName = FileName::findOrFail($request->file_name_id)->name;
        $uploadedFile = $request->file('uploaded_file');

        $extension = $uploadedFile->getClientOriginalExtension();
        $originalName = $uploadedFile->getClientOriginalName();

        $finalName = ($prefix ? $prefix . ' ' : '') . $baseName . ($suffix ? ' ' . $suffix : '');
        $finalFileName = $finalName . '.' . $extension;

        // Verificar duplicado en la carpeta
        $exists = File::where('folder_id', $request->folder_id)
            ->where('prefix', $prefix ?: null)
            ->where('suffix', $suffix ?: null)
            ->where('file_name_id', $request->file_name_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', '⚠️ Ya existe un archivo con el mismo nombre en esta carpeta.');
        }

        // Guardar en disco
        $uploadedFile->storeAs('public/files', $finalFileName);

        // Guardar en BD
        File::create([
            'file_name_id' => $request->file_name_id,
            'prefix' => $prefix ?: null,
            'suffix' => $suffix ?: null,
            'name_original' => $originalName,
            'name_stored' => $finalFileName,
            'type' => strtoupper($extension),
            'folder_id' => $request->folder_id,
            'user_id' => auth()->id(),
        ]);

        return $request->input('from') === 'explorer'
            ? redirect()->route('folders.explorer', ['id' => $request->folder_id])->with('success', '✅ Archivo subido correctamente.')
            : redirect()->route('files.index')->with('success', '✅ Archivo subido correctamente.');
    }


    public function show(File $file, Request $request)
    {
        return view('files.show', compact('file'))
            ->with('from', $request->input('from'));
    }

    public function destroy(Request $request, File $file)
    {
        // Obtener el nombre completo del archivo
        $nombreCompleto = $file->nombre_completo;  // Usa el accesor para obtener el nombre completo

        // Obtén el prefijo y sufijo
        $prefijo = $file->prefix;
        $sufijo = $file->suffix;

        // Elimina el archivo
        $folderId = $file->folder_id;
        $file->delete();

        // Luego puedes pasar estos valores a la vista con la redirección
        return $request->input('from') === 'explorer'
            ? redirect()->route('folders.explorer', ['id' => $folderId])
                ->with('success', "Archivo '$nombreCompleto' con prefijo '$prefijo' y sufijo '$sufijo' eliminado correctamente")
            : redirect()->route('files.index')
                ->with('success', "Archivo '$nombreCompleto' con prefijo '$prefijo' y sufijo '$sufijo' eliminado correctamente");
    }


    public function edit(File $file, Request $request)
    {
        $fileNames = FileName::all();

        $allFolders = Folder::with('parent')
            ->orderByRaw("
            CASE 
                WHEN name ~ '^[0-9]+\\.-' THEN 0
                ELSE 1
            END,
            CASE 
                WHEN name ~ '^[0-9]+\\.-' THEN (string_to_array(name, '.-'))[1]::INTEGER
                ELSE NULL
            END,
            name
        ")
            ->get();

        $parentFolders = Folder::whereNull('parent_id')
            ->orderByRaw("
            CASE 
                WHEN name ~ '^[0-9]+\\.-' THEN 0
                ELSE 1
            END,
            CASE 
                WHEN name ~ '^[0-9]+\\.-' THEN (string_to_array(name, '.-'))[1]::INTEGER
                ELSE NULL
            END,
            name
        ")
            ->get();

        // Obtener la carpeta actual del archivo
        $currentFolderId = $request->input('folder_id', $file->folder_id);
        $currentFolder = Folder::find($currentFolderId);

        // Construcción del breadcrumb de navegación
        $breadcrumb = collect();
        if ($currentFolder) {
            $breadcrumb = collect($currentFolder->getAncestors())->map(function ($folder) {
                return ['id' => $folder->id, 'name' => $folder->name];
            });

            $breadcrumb->push(['id' => $currentFolder->id, 'name' => $currentFolder->name]);
        }

        return view('files.edit', compact('file', 'fileNames', 'allFolders', 'parentFolders', 'breadcrumb', 'currentFolderId'));
    }

    public function update(Request $request, File $file)
    {
        $request->validate([
            'file_name_id' => 'required|exists:file_names,id',
            'folder_id' => 'required|exists:folders,id',
        ]);

        $prefix = trim($request->input('prefix'));
        $suffix = trim($request->input('suffix'));
        $baseName = FileName::findOrFail($request->file_name_id)->name;
        $extension = strtolower($file->type);

        $finalName = ($prefix ? $prefix . ' ' : '') . $baseName . ($suffix ? ' ' . $suffix : '');
        $finalFileName = $finalName . '.' . $extension;

        // Renombrar archivo físico si cambió el nombre
        $oldPath = storage_path("app/public/files/{$file->name_original}");
        $newPath = storage_path("app/public/files/{$finalFileName}");

        if ($file->name_original !== $finalFileName && file_exists($oldPath)) {
            rename($oldPath, $newPath);
        }

        // Actualizar datos en la base de datos
        $file->update([
            'file_name_id' => $request->file_name_id,
            'prefix' => $prefix ?: null,
            'suffix' => $suffix ?: null,
            'folder_id' => $request->folder_id,
            'name_original' => $finalFileName,
        ]);

        return $request->input('from') === 'explorer'
            ? redirect()->route('folders.explorer', ['id' => $file->folder_id])->with('success', 'Archivo actualizado correctamente.')
            : redirect()->route('files.index')->with('success', 'Archivo actualizado correctamente.');
    }

    public function download(File $file)
    {
        // 🔹 Si está en la nube
        if ($file->storage_url) {
            return redirect($file->storage_url);
        }

        // 🔹 Usar `name_stored` para ubicar el archivo
        $filePath = storage_path('app/public/files/' . $file->name_stored);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'El archivo no existe en el servidor.');
        }

        // 🔹 Descargar con el nombre visible bonito
        return response()->download($filePath, $file->nombre_completo . '.' . strtolower($file->type));
    }


    // Método para visualizar un archivo
    public function preview($id)
    {
        $file = File::findOrFail($id);

        // Verifica permisos
        if (auth()->id() !== $file->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        $extension = strtolower($file->type);
        $previewUrl = asset("storage/files/{$file->name_stored}");

        // Tipos compatibles
        $supported = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];

        if (!in_array($extension, $supported)) {
            return view('files.unsupported', compact('file'));
        }

        return view('files.preview', compact('file', 'previewUrl', 'extension'));
    }
    // probar que datos no mas estan mostrandose en la vista
//     public function download(File $file)
// {
//     dd([
//         'file_id' => $file->id,
//         'file_name_original' => $file->name_original,
//         'file_name_predefined' => $file->file_name->name,
//         'file_type' => $file->type,
//         'file_path' => storage_path('app/public/files/' . $file->name_original),
//         'file_exists' => file_exists(storage_path('app/public/files/' . $file->name_original)) ? 'Sí' : 'No'
//     ]);
// }



}
