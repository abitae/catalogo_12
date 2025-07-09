<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

trait FileUploadTrait
{
    use WithFileUploads;

    /**
     * Procesa la carga de una imagen
     */
    protected function processImage($tempImage, $oldImage = null, $path = 'images')
    {
        if (!$tempImage) {
            return $oldImage;
        }

        // Eliminar imagen anterior si existe
        if ($oldImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        // Guardar nueva imagen
        return $tempImage->store($path, 'public');
    }

    /**
     * Procesa la carga de un archivo
     */
    protected function processFile($tempFile, $oldFile = null, $path = 'archivos')
    {
        if (!$tempFile) {
            return $oldFile;
        }

        // Eliminar archivo anterior si existe
        if ($oldFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        // Guardar nuevo archivo
        return $tempFile->store($path, 'public');
    }

    /**
     * Elimina un archivo del storage
     */
    protected function deleteFile($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return true;
        }
        return false;
    }

    /**
     * Valida una imagen
     */
    protected function validateImage($image, $maxSize = 20480)
    {
        return [
            $image => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:{$maxSize}"
        ];
    }

    /**
     * Valida un archivo
     */
    protected function validateFile($file, $maxSize = 20480)
    {
        return [
            $file => "nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:{$maxSize}"
        ];
    }

    /**
     * Mensajes de validación para archivos
     */
    protected function getFileValidationMessages()
    {
        return [
            'tempImage.image' => 'El archivo debe ser una imagen válida',
            'tempImage.mimes' => 'La imagen debe ser en formato: jpeg, png, jpg, gif o svg',
            'tempImage.max' => 'La imagen no debe superar los 20MB',
            'tempArchivo.file' => 'El archivo adjunto no es válido',
            'tempArchivo.mimes' => 'El archivo debe ser en formato: pdf, doc, docx, xls, xlsx, ppt o pptx',
            'tempArchivo.max' => 'El archivo no debe superar los 20MB',
            'tempArchivo2.file' => 'El archivo adjunto no es válido',
            'tempArchivo2.mimes' => 'El archivo debe ser en formato: pdf, doc, docx, xls, xlsx, ppt o pptx',
            'tempArchivo2.max' => 'El archivo no debe superar los 20MB',
        ];
    }
}
