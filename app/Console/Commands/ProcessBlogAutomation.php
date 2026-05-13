<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PostAutomationService;
use App\Models\Tenant;

class ProcessBlogAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:process-blog-automation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process AI blog post generation and publishing for all tenants';

    /**
     * Execute the console command.
     */
    public function handle(PostAutomationService $automationService)
    {
        $this->info('Starting AI Blog Automation...');

        // Percorre todos os tenants
        Tenant::all()->runForEach(function () use ($automationService) {
            $this->info("Processing tenant: " . tenancy()->tenant->id);
            $automationService->processAutomation();
        });

        $this->info('AI Blog Automation completed.');
    }
}
