<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        $mapbox_token = SiteSetting::get('mapbox_public_token', '');
        $google_analytics_id = SiteSetting::get('google_analytics_id', '');
        
        // AI Settings
        $openai_api_key = SiteSetting::get('openai_api_key', '');
        $openai_model = SiteSetting::get('openai_model', 'gpt-4o');
        $openai_base_url = SiteSetting::get('openai_base_url', 'https://api.openai.com/v1');
        
        // Communication (WhatsApp API)
        $wuzapi_api_key = SiteSetting::get('wuzapi_api_key', 'admin');
        $wuzapi_base_url = SiteSetting::get('wuzapi_base_url', 'http://wuzapi:8080');
        $superadmin_whatsapp = SiteSetting::get('superadmin_whatsapp', '');

        // Asaas Settings
        $asaas_api_key = SiteSetting::get('asaas_api_key', '');
        $asaas_api_url = SiteSetting::get('asaas_api_url', 'https://sandbox.asaas.com/api/v3');
        $asaas_environment = SiteSetting::get('asaas_environment', 'sandbox');
        $asaas_wallet_id = SiteSetting::get('asaas_wallet_id', '');
        
        return view('admin.settings.index', compact(
            'mapbox_token', 
            'google_analytics_id', 
            'openai_api_key', 
            'openai_model', 
            'openai_base_url',
            'wuzapi_api_key',
            'wuzapi_base_url',
            'superadmin_whatsapp',
            'asaas_api_key',
            'asaas_api_url',
            'asaas_environment',
            'asaas_wallet_id'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mapbox_public_token' => 'nullable|string',
            'google_analytics_id' => 'nullable|string',
            'openai_api_key' => 'nullable|string',
            'openai_model' => 'nullable|string',
            'openai_base_url' => 'nullable|string|url',
            'wuzapi_api_key' => 'nullable|string',
            'wuzapi_base_url' => 'nullable|string|url',
            'superadmin_whatsapp' => 'nullable|string',
            'asaas_api_key' => 'nullable|string',
            'asaas_api_url' => 'nullable|string|url',
            'asaas_environment' => 'nullable|string|in:sandbox,production',
            'asaas_wallet_id' => 'nullable|string',
        ]);

        SiteSetting::set('mapbox_public_token', $request->mapbox_public_token, 'text', 'Mapbox Public Token', true);
        SiteSetting::set('google_analytics_id', $request->google_analytics_id, 'text', 'Google Analytics ID', true);
        
        SiteSetting::set('openai_api_key', $request->openai_api_key, 'text', 'OpenAI API Key', true);
        SiteSetting::set('openai_model', $request->openai_model, 'text', 'OpenAI Model', true);
        SiteSetting::set('openai_base_url', $request->openai_base_url, 'text', 'OpenAI Base URL', true);
        
        SiteSetting::set('wuzapi_api_key', $request->wuzapi_api_key, 'text', 'Wuzapi API Key', true);
        SiteSetting::set('wuzapi_base_url', $request->wuzapi_base_url, 'text', 'Wuzapi Base URL', true);
        SiteSetting::set('superadmin_whatsapp', $request->superadmin_whatsapp, 'text', 'SuperAdmin WhatsApp', true);

        SiteSetting::set('asaas_api_key', $request->asaas_api_key, 'text', 'Asaas API Key', false);
        SiteSetting::set('asaas_api_url', $request->asaas_api_url, 'text', 'Asaas API URL', false);
        SiteSetting::set('asaas_environment', $request->asaas_environment, 'text', 'Asaas Environment', false);
        SiteSetting::set('asaas_wallet_id', $request->asaas_wallet_id, 'text', 'Asaas Wallet ID', false);

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');
    }
}
