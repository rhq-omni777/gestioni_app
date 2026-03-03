<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()?->role !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function edit(): View
    {
        $settings = $this->settingsMap();
        return view('settings.edit', $settings);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'max_login_attempts' => ['required', 'integer', 'min:1', 'max:20'],
            'uploads_max_size' => ['required', 'integer', 'min:1', 'max:200'],
            'mail_from_address' => ['required', 'email'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        $this->set('app.name', ['value' => $data['app_name']], 'branding');
        $this->set('security.max_attempts', ['value' => $data['max_login_attempts']], 'security');
        $this->set('uploads.max_size_mb', ['value' => $data['uploads_max_size']], 'files');
        $this->set('mail.from', ['address' => $data['mail_from_address'], 'name' => $data['mail_from_name']], 'mail', 'json');

        return redirect()->route('settings.edit')->with('status', 'Configuración actualizada');
    }

    private function settingsMap(): array
    {
        return [
            'appName' => Setting::where('key', 'app.name')->first()?->value['value'] ?? config('app.name'),
            'maxLoginAttempts' => Setting::where('key', 'security.max_attempts')->first()?->value['value'] ?? 5,
            'uploadsMaxSize' => Setting::where('key', 'uploads.max_size_mb')->first()?->value['value'] ?? 10,
            'mailFromAddress' => Setting::where('key', 'mail.from')->first()?->value['address'] ?? config('mail.from.address'),
            'mailFromName' => Setting::where('key', 'mail.from')->first()?->value['name'] ?? config('mail.from.name'),
        ];
    }

    private function set(string $key, array $value, string $group = 'general', string $type = 'json'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'group' => $group,
                'value' => $value,
                'type' => $type,
                'autoload' => true,
            ]
        );
    }
}
