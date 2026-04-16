<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Athlete;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $urls = [];

        // Home page
        $urls[] = [
            'loc' => route('site.home'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // About page
        $urls[] = [
            'loc' => route('site.about'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ];

        // Teams page
        $urls[] = [
            'loc' => route('site.teams'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];

        // Individual teams
        try {
            $teams = Team::where('is_active', true)->get();
            foreach ($teams as $team) {
                $urls[] = [
                    'loc' => route('site.team', $team),
                    'lastmod' => $team->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        } catch (\Exception $e) {
            // Se tabela não existir, pular
        }

        // Athletes page
        $urls[] = [
            'loc' => route('site.athletes'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];

        // Individual athletes
        try {
            $athletes = Athlete::where('is_active', true)->get();
            foreach ($athletes as $athlete) {
                $urls[] = [
                    'loc' => route('site.athlete', $athlete),
                    'lastmod' => $athlete->updated_at->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            }
        } catch (\Exception $e) {
            // Se tabela não existir, pular
        }

        // Store page
        $urls[] = [
            'loc' => route('site.store'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.8',
        ];

        // Individual products
        try {
            $products = Product::where('is_active', true)->get();
            foreach ($products as $product) {
                $urls[] = [
                    'loc' => route('site.product', $product),
                    'lastmod' => $product->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        } catch (\Exception $e) {
            // Se tabela não existir, pular
        }

        // Contact page
        $urls[] = [
            'loc' => route('site.contact'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.6',
        ];

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}

