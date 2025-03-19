<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PixAccount;
use App\Models\StripeAccount;
use Illuminate\Support\Facades\Auth;

class PixAccountController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
    
        // 🔹 Buscar a conta Pix do usuário autenticado
        $pixAccount = PixAccount::where('user_id', $user->id)->first();
    
        // 🔹 Buscar a conta Stripe do usuário autenticado
        $stripeAccount = StripeAccount::where('user_id', $user->id)->first();
    
        return view('admin.pix_account.edit', compact('pixAccount', 'stripeAccount'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|in:Banco do Brasil,Sicoob',
            'pix_key' => 'required|string',
            'pix_key_type' => 'required|string|in:cpf,cnpj,email,telefone,aleatoria',
            'bb_client_id' => 'nullable|string',
            'bb_client_secret' => 'nullable|string',
            'bb_gw_app_key' => 'nullable|string',
            'sicoob_client_id' => 'nullable|string',
            'sicoob_access_token' => 'nullable|string',
            'stripe_public_key' => 'nullable|string',
            'stripe_secret_key' => 'nullable|string',
            'stripe_webhook_secret' => 'nullable|string',
        ]);

        $user = Auth::user();
        $pixAccount = PixAccount::where('user_id', $user->id)->first();
        $stripeAccount = StripeAccount::where('user_id', $user->id)->first();

        $data = [
            'bank_name' => $request->bank_name,
            'pix_key' => $request->pix_key,
            'pix_key_type' => $request->pix_key_type,
        ];

        // 🔹 Se o banco for "Banco do Brasil", adiciona os campos específicos
        if ($request->bank_name === "Banco do Brasil") {
            $data['bb_client_id'] = $request->bb_client_id;
            $data['bb_client_secret'] = $request->bb_client_secret;
            $data['bb_gw_app_key'] = $request->bb_gw_app_key;
            // 🔹 Limpa os campos do Sicoob se mudou de banco
            $data['sicoob_client_id'] = null;
            $data['sicoob_access_token'] = null;
        } 
        // 🔹 Se for Sicoob, adiciona os campos do Sicoob e limpa os do Banco do Brasil
        elseif ($request->bank_name === "Sicoob") {
            $data['sicoob_client_id'] = $request->sicoob_client_id;
            $data['sicoob_access_token'] = $request->sicoob_access_token;
            $data['bb_client_id'] = null;
            $data['bb_client_secret'] = null;
            $data['bb_gw_app_key'] = null;
        }

        if ($pixAccount) {
            // Atualiza a conta Pix existente
            $pixAccount->update($data);
        } else {
            // Cria uma nova conta Pix se não existir
            PixAccount::create(array_merge($data, ['user_id' => $user->id]));
        }

        // 🔹 Atualiza a conta Stripe se houver dados preenchidos
        if ($request->filled('stripe_public_key') || $request->filled('stripe_secret_key') || $request->filled('stripe_webhook_secret')) {
            $stripeData = [
                'stripe_public_key' => $request->stripe_public_key,
                'stripe_secret_key' => $request->stripe_secret_key,
                'stripe_webhook_secret' => $request->stripe_webhook_secret,
                'user_id' => $user->id
            ];

            if ($stripeAccount) {
                $stripeAccount->update($stripeData);
            } else {
                StripeAccount::create($stripeData);
            }
        }

        return redirect()->back()->with('success', 'Conta Pix e Stripe atualizadas com sucesso.');
    }
}
