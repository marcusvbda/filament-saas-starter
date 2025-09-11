<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class IntegrationsSettings extends Settings
{
    public array $docusign = [
        "oauth_uri" => "https://account-d.docusign.com",
        "base_uri" => "https://demo.docusign.net",
        "integration_key" => "",
        "events_webhook_url" => ""
    ];

    public static function group(): string
    {
        return 'integrations';
    }
}
