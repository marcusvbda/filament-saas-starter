<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class IntegrationsSettings extends Settings
{
    public array $email = [
        // "host" => "",
        // "port" => "",
        // "user_name" => "",
        // "password" => ""
    ];

    public static function group(): string
    {
        return 'integrations';
    }
}
