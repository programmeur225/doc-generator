<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected array $keys = ['site_name', 'support_email', 'footer_text', 'maintenance_mode'];

    public function edit()
    {
        $settings = Setting::whereIn('key', $this->keys)->pluck('value', 'key');

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email', 'max:255'],
            'footer_text' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Paramètres mis à jour.');
    }
}
