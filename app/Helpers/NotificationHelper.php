<?php

namespace App\Helpers;

class NotificationHelper
{
    /**
     * Flash success message to session
     *
     * @param string $message
     * @return void
     */
    public static function success($message)
    {
        session()->flash('success', $message);
    }

    /**
     * Flash error message to session
     *
     * @param string $message
     * @return void
     */
    public static function error($message)
    {
        session()->flash('error', $message);
    }

    /**
     * Flash info message to session
     *
     * @param string $message
     * @return void
     */
    public static function info($message)
    {
        session()->flash('info', $message);
    }

    /**
     * Flash warning message to session
     *
     * @param string $message
     * @return void
     */
    public static function warning($message)
    {
        session()->flash('warning', $message);
    }
} 