<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{
    /**
     * Display the website settings page.
     */
    public function index()
    {
        $websiteName = Setting::getValue('website_name', 'Absensi');
        $websiteLogo = Setting::getValue('website_logo_base64', '');
        
        return view('admin.website_settings.index', compact('websiteName', 'websiteLogo'));
    }

    /**
     * Update website settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'website_name' => 'required|string|max:255',
            'website_logo' => 'nullable|image|max:500', // max 500kb
        ], [
            'website_name.required' => 'Nama website wajib diisi.',
            'website_logo.image' => 'Logo harus berupa gambar.',
            'website_logo.max' => 'Ukuran logo tidak boleh lebih dari 500Kb.',
        ]);

        Setting::setValue('website_name', $request->website_name);

        if ($request->hasFile('website_logo')) {
            $logo = $request->file('website_logo');
            $logoBase64 = base64_encode(file_get_contents($logo->getRealPath()));
            $mime = $logo->getMimeType();
            $base64String = "data:{$mime};base64,{$logoBase64}";
            Setting::setValue('website_logo_base64', $base64String);
        }

        return redirect()->back()->with('success', 'Pengaturan website berhasil diperbarui!');
    }
}
