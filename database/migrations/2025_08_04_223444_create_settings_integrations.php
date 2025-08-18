<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrator->add('integrations.email', [
            "host" => "",
            "port" => "",
            "user_name" => "",
            "password" => ""
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->migrator->delete('integrations.email');
    }
};
