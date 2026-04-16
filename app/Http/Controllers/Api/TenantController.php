<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    /**
     * Check subdomain availability.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSubdomain(Request $request)
    {
        $request->validate([
            'subdomain' => 'required|string|max:255',
        ]);

        $subdomain = strtolower(trim($request->input('subdomain')));

        // Check if subdomain is empty
        if (empty($subdomain)) {
            return response()->json([
                'available' => false,
                'message' => 'Subdomínio é obrigatório'
            ], 400);
        }

        // Check if subdomain matches valid pattern
        if (!preg_match('/^[a-z0-9-]+$/', $subdomain)) {
            return response()->json([
                'available' => false,
                'message' => 'Subdomínio deve conter apenas letras minúsculas, números e hífens'
            ], 400);
        }

        // Check if subdomain is too short
        if (strlen($subdomain) < 3) {
            return response()->json([
                'available' => false,
                'message' => 'Subdomínio deve ter pelo menos 3 caracteres'
            ], 400);
        }

        // Check if subdomain is too long
        if (strlen($subdomain) > 63) {
            return response()->json([
                'available' => false,
                'message' => 'Subdomínio deve ter no máximo 63 caracteres'
            ], 400);
        }

        // Check reserved subdomains
        $reserved = ['www', 'admin', 'api', 'app', 'mail', 'ftp', 'localhost', 'test', 'dev', 'staging', 'prod'];
        if (in_array($subdomain, $reserved)) {
            return response()->json([
                'available' => false,
                'message' => 'Este subdomínio está reservado e não pode ser usado'
            ], 400);
        }

        // Check if subdomain is already taken
        $exists = Tenant::where('subdomain', $subdomain)->exists();

        if ($exists) {
            Log::info('Subdomain check failed: already exists', ['subdomain' => $subdomain]);
            
            return response()->json([
                'available' => false,
                'message' => 'Subdomínio já está em uso'
            ]);
        }

        Log::info('Subdomain check successful: available', ['subdomain' => $subdomain]);

        return response()->json([
            'available' => true,
            'message' => 'Subdomínio disponível'
        ]);
    }
}

