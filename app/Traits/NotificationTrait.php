<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

trait NotificationTrait
{
    /**
     * Envía una notificación de éxito
     */
    protected function notifySuccess($message, $title = null)
    {
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message,
            'title' => $title
        ]);
    }

    /**
     * Envía una notificación de error
     */
    protected function notifyError($message, $title = null)
    {
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => $message,
            'title' => $title
        ]);
    }

    /**
     * Envía una notificación de advertencia
     */
    protected function notifyWarning($message, $title = null)
    {
        $this->dispatch('notify', [
            'type' => 'warning',
            'message' => $message,
            'title' => $title
        ]);
    }

    /**
     * Envía una notificación de información
     */
    protected function notifyInfo($message, $title = null)
    {
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => $message,
            'title' => $title
        ]);
    }

    /**
     * Maneja errores de forma consistente
     */
    protected function handleError(\Exception $e, $context = '')
    {
        $message = $context ? "Error en {$context}: " . $e->getMessage() : $e->getMessage();

        $this->notifyError($message);

        // Log del error para debugging
        Log::error($message, [
            'exception' => $e,
            'context' => $context,
            'user_id' => Auth::id(),
            'url' => request()->url(),
        ]);
    }

    /**
     * Maneja operaciones exitosas de forma consistente
     */
    protected function handleSuccess($message, $context = '')
    {
        $this->notifySuccess($message);

        // Log de la operación exitosa
        Log::info("Operación exitosa: {$message}", [
            'context' => $context,
            'user_id' => Auth::id(),
            'url' => request()->url(),
        ]);
    }
}
