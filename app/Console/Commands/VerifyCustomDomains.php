<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain;
use Illuminate\Support\Facades\Log;

class VerifyCustomDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica se os domínios customizados dos tenants já foram apontados corretamente (DNS CNAME/A).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificação de domínios...');

        // Pegar todos os domínios customizados (não principais) que ainda não estão verificados
        $domains = Domain::where('is_primary', false)
            ->where('is_verified', false)
            ->get();

        if ($domains->isEmpty()) {
            $this->info('Nenhum domínio pendente de verificação.');
            return 0;
        }

        // O host de destino padrão que o tenant deve apontar (o CNAME destination)
        $centralDomain = config('tenancy.central_domains')[0] ?? 'real.nexts.conext.click';

        foreach ($domains as $domainRecord) {
            $domain = $domainRecord->domain;
            $this->info("Verificando: {$domain}");

            try {
                // Tenta resolver o DNS (registros CNAME, A, ou AAAA)
                $records = dns_get_record($domain, DNS_CNAME | DNS_A | DNS_AAAA);
                
                $isPointingCorrectly = false;

                foreach ($records as $record) {
                    if ($record['type'] === 'CNAME' && isset($record['target'])) {
                        // Verifica se o CNAME aponta para o domínio central
                        if (str_contains(strtolower($record['target']), strtolower($centralDomain))) {
                            $isPointingCorrectly = true;
                            break;
                        }
                    } elseif ($record['type'] === 'A' || $record['type'] === 'AAAA') {
                        // Caso aponte via IP, consideramos válido temporariamente (o ideal seria comparar IPs)
                        // Para simplificar no SaaS, se tem registro A ativo que resolve para o nosso IP, é válido.
                        // Mas CNAME é o método primário. Vamos considerar válido se resolver via CNAME para o app.
                        
                        // Busca o IP do domínio central
                        $centralIp = gethostbyname($centralDomain);
                        if (isset($record['ip']) && $record['ip'] === $centralIp) {
                            $isPointingCorrectly = true;
                            break;
                        }
                    }
                }

                // Fallback: usar gethostbyname para checar se resolve pro mesmo IP do servidor
                if (!$isPointingCorrectly) {
                    $domainIp = gethostbyname($domain);
                    $centralIp = gethostbyname($centralDomain);
                    if ($domainIp !== $domain && $domainIp === $centralIp) {
                        $isPointingCorrectly = true;
                    }
                }

                if ($isPointingCorrectly) {
                    $domainRecord->update(['is_verified' => true]);
                    $this->info("✓ Domínio {$domain} verificado com sucesso!");
                    Log::info("Domínio customizado verificado automaticamente: {$domain}");
                } else {
                    $this->warn("✗ Domínio {$domain} ainda não propagou ou não aponta para o servidor correto.");
                }

            } catch (\Exception $e) {
                $this->error("Erro ao verificar {$domain}: " . $e->getMessage());
            }
        }

        $this->info('Verificação concluída.');
        return 0;
    }
}
