<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // SuperAdmin contact info from SiteSettings
        $contact_email = SiteSetting::get('superadmin_email', $user->email);
        $contact_phone = SiteSetting::get('superadmin_phone', '');
        $contact_whatsapp = SiteSetting::get('superadmin_whatsapp', '');
        $contact_address = SiteSetting::get('superadmin_address', '');
        $social_instagram = SiteSetting::get('superadmin_instagram', '');
        $social_facebook = SiteSetting::get('superadmin_facebook', '');
        $social_linkedin = SiteSetting::get('superadmin_linkedin', '');

        return view('admin.profile.index', compact(
            'user',
            'contact_email',
            'contact_phone',
            'contact_whatsapp',
            'contact_address',
            'social_instagram',
            'social_facebook',
            'social_linkedin'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
            'password' => 'nullable|min:8|confirmed',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'contact_whatsapp' => 'nullable|string',
            'contact_address' => 'nullable|string',
            'social_instagram' => 'nullable|string',
            'social_facebook' => 'nullable|string',
            'social_linkedin' => 'nullable|string',
        ]);

        // Update basic info
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            $path = $request->file('avatar')->storeOptimized('avatars');
            $user->avatar = $path;
        }

        $user->save();

        // Update SiteSettings for contact info (used in marketing site)
        SiteSetting::set('superadmin_email', $request->contact_email, 'text', 'SuperAdmin Contact Email', true);
        SiteSetting::set('superadmin_phone', $request->contact_phone, 'text', 'SuperAdmin Contact Phone', true);
        SiteSetting::set('superadmin_whatsapp', $request->contact_whatsapp, 'text', 'SuperAdmin Contact WhatsApp', true);
        SiteSetting::set('superadmin_address', $request->contact_address, 'text', 'SuperAdmin Contact Address', true);
        SiteSetting::set('superadmin_instagram', $request->social_instagram, 'text', 'SuperAdmin Instagram', true);
        SiteSetting::set('superadmin_facebook', $request->social_facebook, 'text', 'SuperAdmin Facebook', true);
        SiteSetting::set('superadmin_linkedin', $request->social_linkedin, 'text', 'SuperAdmin LinkedIn', true);

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}
