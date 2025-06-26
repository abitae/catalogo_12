<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Envía una notificación de éxito
     */
    public static function success($message, $title = null)
    {
        self::dispatch('success', $message, $title);
    }

    /**
     * Envía una notificación de error
     */
    public static function error($message, $title = null)
    {
        self::dispatch('error', $message, $title);
        Log::error($message);
    }

    /**
     * Envía una notificación de advertencia
     */
    public static function warning($message, $title = null)
    {
        self::dispatch('warning', $message, $title);
    }

    /**
     * Envía una notificación de información
     */
    public static function info($message, $title = null)
    {
        self::dispatch('info', $message, $title);
    }

    /**
     * Despacha la notificación
     */
    private static function dispatch($type, $message, $title = null)
    {
        $notification = [
            'type' => $type,
            'message' => $message,
            'title' => $title,
            'timestamp' => now()->toISOString(),
        ];

        // En un entorno real, aquí podrías enviar a un sistema de notificaciones
        // como websockets, eventos, etc.

        if (request()->hasHeader('X-Livewire')) {
            // Si es una petición Livewire, usar el sistema de eventos
            request()->session()->flash('notification', $notification);
        }
    }

    /**
     * Obtiene las notificaciones pendientes
     */
    public static function getNotifications()
    {
        return request()->session()->get('notification');
    }

    /**
     * Limpia las notificaciones
     */
    public static function clear()
    {
        request()->session()->forget('notification');
    }
}
