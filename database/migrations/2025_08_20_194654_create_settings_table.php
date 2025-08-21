<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrator->add('integrations.docusign', [
            "oauth_uri" => "",
            "base_uri" => "",
            "integration_key" => "",
            "events_webhook_url" => ""
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->migrator->delete('integrations.docusign');
    }
};
