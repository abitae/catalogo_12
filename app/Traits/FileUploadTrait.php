<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

trait FileUploadTrait
{
    /**
     * Procesa y almacena una imagen de forma segura
     */
    protected function processImage(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        // Eliminar archivo anterior si existe
        if ($oldPath) {
            $this->deleteFileFromStorage($oldPath);
        }

        // Generar nombre único para el archivo
        $fileName = $this->generateUniqueFileName($file);

        // Almacenar en el directorio especificado
        return $file->storeAs($directory, $fileName, 'public');
    }

    /**
     * Procesa y almacena un archivo de forma segura
     */
    protected function processFile(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        // Eliminar archivo anterior si existe
        if ($oldPath) {
            $this->deleteFileFromStorage($oldPath);
        }

        // Generar nombre único para el archivo
        $fileName = $this->generateUniqueFileName($file);

        // Almacenar en el directorio especificado
        return $file->storeAs($directory, $fileName, 'public');
    }

    /**
     * Elimina un archivo del storage de forma segura
     */
    protected function deleteFileFromStorage(?string $filePath): bool
    {
        if (!$filePath) {
            return false;
        }

        try {
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->delete($filePath);
            }
            return false;
        } catch (\Exception $e) {
            Log::warning('Error al eliminar archivo: ' . $e->getMessage(), [
                'file_path' => $filePath,
                'user_id' => Auth::id() ?? 'guest'
            ]);
            return false;
        }
    }

    /**
     * Genera un nombre único para el archivo
     */
    protected function generateUniqueFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = Str::slug($baseName);

        return $sanitizedName . '_' . time() . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Valida si un archivo es una imagen válida
     */
    protected function isValidImage(UploadedFile $file): bool
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
        return in_array($file->getMimeType(), $allowedMimes);
    }

    /**
     * Valida si un archivo es un documento válido
     */
    protected function isValidDocument(UploadedFile $file): bool
    {
        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];
        return in_array($file->getMimeType(), $allowedMimes);
    }

    /**
     * Obtiene la URL pública de un archivo
     */
    protected function getFileUrl(?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        try {
            return asset('storage/' . $filePath);
        } catch (\Exception $e) {
            Log::warning('Error al obtener URL del archivo: ' . $e->getMessage(), [
                'file_path' => $filePath
            ]);
            return null;
        }
    }

    /**
     * Verifica si un archivo existe en el storage
     */
    protected function fileExists(?string $filePath): bool
    {
        if (!$filePath) {
            return false;
        }

        try {
            return Storage::disk('public')->exists($filePath);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtiene el tamaño de un archivo en formato legible
     */
    protected function getFileSize(?string $filePath): string
    {
        if (!$filePath || !$this->fileExists($filePath)) {
            return '0 B';
        }

        try {
            $size = Storage::disk('public')->size($filePath);
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return '0 B';
        }
    }

    /**
     * Formatea bytes a formato legible
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Limpia archivos temporales no utilizados
     */
    protected function cleanupTempFiles(): void
    {
        try {
            $tempDirectory = 'temp/' . Auth::id();
            if (Storage::disk('public')->exists($tempDirectory)) {
                $files = Storage::disk('public')->files($tempDirectory);
                $cutoff = now()->subHours(24);

                foreach ($files as $file) {
                    $lastModified = Storage::disk('public')->lastModified($file);
                    if ($lastModified < $cutoff->timestamp) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error al limpiar archivos temporales: ' . $e->getMessage());
        }
    }
}
