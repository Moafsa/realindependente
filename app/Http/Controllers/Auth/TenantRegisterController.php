<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class TenantRegisterController extends Controller
{
    private \App\Services\CartService $cartService;

    public function __construct(\App\Services\CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Show the athlete registration form.
     */
    public function showRegistrationForm(Request $request)
    {
        $plan_id = $request->query('plan_id');
        $plan = null;
        if ($plan_id) {
            $plan = \App\Models\Product::find($plan_id);
        }

        $legal = tenancy()->central(function () {
            $terms = \App\Models\SiteSetting::get('global_terms_of_use', 'Estes são os termos de uso padrão.');
            $insurance = \App\Models\SiteSetting::get('global_insurance_policy', 'Detalhes do seguro atleta.');
            return compact('terms', 'insurance');
        });

        // Personalização
        $replacements = [
            '{school_name}' => tenant('name') ?? 'Nossa Escolinha',
            '{school_email}' => tenant('email') ?? 'contato@escola.com',
            // Adicione mais se necessário (ex: telefone, endereço se estiverem no tenant->data)
        ];

        $legal['terms'] = strtr($legal['terms'], $replacements);
        $legal['insurance'] = strtr($legal['insurance'], $replacements);

        return view('site.register', compact('legal', 'plan'));
    }

    /**
     * Handle an athlete registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|string|in:masculino,feminino',
            'document' => 'required|string|regex:/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'positions' => 'required|array|min:1',
            'height' => 'nullable|numeric|min:0.5|max:2.5',
            'weight' => 'nullable|numeric|min:10|max:200',
            'medical_conditions' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'guardian_name' => 'required|string|max:255',
            'guardian_document' => 'required|string|regex:/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/',
            'guardian_contact' => 'required|string|max:20',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'terms_accepted' => 'accepted',
            'insurance_accepted' => 'accepted',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create the User
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'athlete',
                'is_active' => true,
                'phone' => $request->phone,
            ]);

            // 2. Prepare Athlete Data
            $athleteData = [
                'full_name' => $request->full_name,
                'document' => preg_replace('/[^0-9]/', '', $request->document),
                'phone' => $request->phone,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'subcategory' => Athlete::calculateSubcategory($request->birth_date),
                'positions' => $request->positions,
                'position' => $request->positions[0], // Keep for backward compatibility
                'height' => $request->height,
                'weight' => $request->weight,
                'medical_conditions' => $request->medical_conditions ? explode(',', $request->medical_conditions) : [],
                'allergies' => $request->allergies ? explode(',', $request->allergies) : [],
                'guardian_name' => $request->guardian_name,
                'guardian_document' => preg_replace('/[^0-9]/', '', $request->guardian_document),
                'guardian_contact' => $request->guardian_contact,
                'guardian_email' => $request->email, // Default to user email
                'user_id' => $user->id,
                'terms_accepted' => true,
                'insurance_accepted' => true,
                'is_active' => true,
            ];

            // 3. Handle Medical Certificate Upload
            if ($request->hasFile('medical_certificate')) {
                $path = $request->file('medical_certificate')->store('medical_certificates', 'public');
                $athleteData['medical_certificate_path'] = $path;
            }

            $athlete = Athlete::create($athleteData);
            
            // Calculate initial profile completion
            $athlete->update([
                'profile_completion' => $athlete->getProfileCompletionPercentage()
            ]);

            DB::commit();

            Auth::login($user);

            // Se selecionou um plano, adiciona ao carrinho e vai para o checkout
            if ($request->filled('plan_id')) {
                $plan = \App\Models\Product::find($request->plan_id);
                if ($plan) {
                    $this->cartService->clear();
                    $this->cartService->add($plan, 1);
                    return redirect()->route('site.checkout');
                }
            }

            return redirect()->route('portal.dashboard')
                ->with('success', 'Bem-vindo! Sua conta de atleta foi criada com sucesso.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocorreu um erro ao processar seu cadastro: ' . $e->getMessage());
        }
    }
}
