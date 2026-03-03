<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@gestioni.local'],
            [
                'name' => 'Administrador',
                'password' => 'admin123',
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'user@gestioni.local'],
            [
                'name' => 'Usuario Demo',
                'password' => 'admin123',
                'role' => 'user',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $manager = User::updateOrCreate(
            ['email' => 'manager@gestioni.local'],
            [
                'name' => 'Manager Demo',
                'password' => 'admin123',
                'role' => 'manager',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'full_name' => 'Administrador',
                'job_title' => 'Admin',
                'timezone' => 'America/Mexico_City',
                'locale' => 'es',
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => 'Usuario Demo',
                'job_title' => 'Colaborador',
                'timezone' => 'America/Mexico_City',
                'locale' => 'es',
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $manager->id],
            [
                'full_name' => 'Manager Demo',
                'job_title' => 'Manager',
                'timezone' => 'America/Mexico_City',
                'locale' => 'es',
            ]
        );

        $defaults = [
            ['group' => 'branding', 'key' => 'app.name', 'value' => ['value' => 'GESTIONI'], 'type' => 'string'],
            ['group' => 'branding', 'key' => 'app.logo', 'value' => ['value' => null], 'type' => 'string'],
            ['group' => 'security', 'key' => 'auth.max_attempts', 'value' => ['value' => 5], 'type' => 'int'],
            ['group' => 'files', 'key' => 'uploads.max_size_mb', 'value' => ['value' => 10], 'type' => 'int'],
            ['group' => 'mail', 'key' => 'mail.from', 'value' => ['address' => 'noreply@gestioni.local', 'name' => 'GESTIONI'], 'type' => 'json'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
