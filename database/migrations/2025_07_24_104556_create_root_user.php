<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Role::create(['name' => 'viewer']);
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'super_admin']);

        $user = User::create([
            'name' => 'Root',
            'email' => 'root@root.com',
            'password' => env("ROOT_PASSWORD"),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('super_admin');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::where('email', 'root@root.com')->delete();
    }
};
