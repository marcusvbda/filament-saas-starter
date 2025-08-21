<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class IntegrationsSettings extends Settings
{
    public array $docusign = [
        "oauth_uri" => "",
        "base_uri" => "",
        "integration_key" => "",
        "events_webhook_url" => ""
    ];

    public static function group(): string
    {
        return 'integrations';
    }
}
