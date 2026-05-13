<?php

namespace App\Http\Controllers;

use App\Services\WuzapiService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected $wuzapi;

    public function __construct(WuzapiService $wuzapi)
    {
        $this->wuzapi = $wuzapi;
    }

    public function index()
    {
        $statusStr = $this->wuzapi->getSessionStatus();
        $status = [
            'connected' => $statusStr === 'CONNECTED',
            'status' => $statusStr,
            'phone' => $statusStr === 'CONNECTED' ? 'WhatsApp Ativo' : null
        ];
        return view('admin.whatsapp.index', compact('status'));
    }

    public function qrCode()
    {
        $result = $this->wuzapi->getQrCode();
        return response()->json($result);
    }

    public function disconnect()
    {
        $success = $this->wuzapi->disconnectSession();
        if ($success) {
            return back()->with('success', 'WhatsApp desconectado com sucesso.');
        }
        return back()->with('error', 'Erro ao desconectar o WhatsApp.');
    }

    public function status()
    {
        $statusStr = $this->wuzapi->getSessionStatus();
        return response()->json([
            'connected' => $statusStr === 'CONNECTED',
            'status' => $statusStr
        ]);
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'wuzapi_api_key' => 'required|string',
            'wuzapi_base_url' => 'nullable|url',
        ]);

        \App\Models\SiteSetting::set('wuzapi_api_key', $request->wuzapi_api_key);
        if ($request->wuzapi_base_url) {
            \App\Models\SiteSetting::set('wuzapi_base_url', $request->wuzapi_base_url);
        }

        return back()->with('success', 'Configurações de WhatsApp salvas com sucesso.');
    }
}
