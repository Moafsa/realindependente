<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    /**
     * Show the marketing landing page.
     */
    public function index()
    {
        $plans = Plan::active()->ordered()->get();
        
        return view('marketing.home', compact('plans'));
    }

    /**
     * Show the features page.
     */
    public function features()
    {
        $features = [
            [
                'title' => 'Gestão Completa',
                'description' => 'Gerencie atletas, equipes, finanças e filiais em uma única plataforma.',
                'icon' => 'users',
                'benefits' => [
                    'Cadastro completo de atletas',
                    'Gestão de equipes e categorias',
                    'Controle financeiro integrado',
                    'Múltiplas filiais'
                ]
            ],
            [
                'title' => 'Portal do Atleta',
                'description' => 'Área exclusiva para atletas acompanharem seu desenvolvimento.',
                'icon' => 'chart-line',
                'benefits' => [
                    'Acompanhamento de performance',
                    'Planos personalizados de IA',
                    'Comunicação com treinadores',
                    'Histórico de evolução'
                ]
            ],
            [
                'title' => 'Site Público',
                'description' => 'Site automático para cada clube com loja virtual integrada.',
                'icon' => 'globe',
                'benefits' => [
                    'Site gerado automaticamente',
                    'Loja virtual integrada',
                    'Pagamentos via PIX e cartão',
                    'Personalização completa'
                ]
            ],
            [
                'title' => 'Inteligência Artificial',
                'description' => 'Planos de treino e nutrição personalizados para cada atleta.',
                'icon' => 'brain',
                'benefits' => [
                    'Planos de treino personalizados',
                    'Planos nutricionais',
                    'Análise de performance',
                    'Recomendações inteligentes'
                ]
            ]
        ];

        return view('marketing.features', compact('features'));
    }

    /**
     * Show the pricing page.
     */
    public function pricing()
    {
        $plans = Plan::active()->ordered()->get();
        
        return view('marketing.pricing', compact('plans'));
    }

    /**
     * Show the contact page.
     */
    public function contact()
    {
        return view('marketing.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // TODO: Implement contact form logic
        // This would typically involve sending an email to the admin

        return back()->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}
