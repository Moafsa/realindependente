<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\AIUsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiMonitoringController extends Controller
{
    protected AIUsageService $usageService;

    public function __construct(AIUsageService $usageService)
    {
        $this->usageService = $usageService;
    }

    public function index()
    {
        $tenants = Tenant::where('status', 'active')->get();
        $monitoringData = [];
        $totalGlobalCost = 0;
        $totalGlobalRequests = 0;

        foreach ($tenants as $tenant) {
            $usage = $this->usageService->getTenantUsage($tenant);
            $monitoringData[] = [
                'tenant' => $tenant,
                'usage' => $usage
            ];
            $totalGlobalCost += $usage['costs'] ?? 0;
            $totalGlobalRequests += $usage['count'] ?? 0;
        }

        // Sort by cost descending
        usort($monitoringData, function($a, $b) {
            return ($b['usage']['costs'] ?? 0) <=> ($a['usage']['costs'] ?? 0);
        });

        return view('admin.ai.monitoring', compact('monitoringData', 'totalGlobalCost', 'totalGlobalRequests'));
    }
}
