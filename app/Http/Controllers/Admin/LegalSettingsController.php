<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class LegalSettingsController extends Controller
{
    public function index()
    {
        $terms = SiteSetting::get('global_terms_of_use', 'Estes são os termos de uso padrão para {school_name}.');
        $insurance = SiteSetting::get('global_insurance_policy', 'Esta é a apólice de seguro padrão para {school_name}.');

        return view('admin.legal.index', compact('terms', 'insurance'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'global_terms_of_use' => 'required|string',
            'global_insurance_policy' => 'required|string',
        ]);

        SiteSetting::set('global_terms_of_use', $request->global_terms_of_use, 'textarea', 'Termos de Uso Globais', true);
        SiteSetting::set('global_insurance_policy', $request->global_insurance_policy, 'textarea', 'Apólice de Seguro Global', true);

        return redirect()->back()->with('success', 'Configurações legais atualizadas com sucesso!');
    }
}
