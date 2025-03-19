<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StripeAccount;
use Illuminate\Support\Facades\Auth;

class StripeAccountController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $stripeAccount = StripeAccount::where('user_id', $user->id)->first();

        return view('admin.stripe_account.edit', compact('stripeAccount'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'stripe_public_key' => 'required|string',
            'stripe_secret_key' => 'required|string',
            'stripe_webhook_secret' => 'required|string',
        ]);

        $user = Auth::user();
        $stripeAccount = StripeAccount::where('user_id', $user->id)->first();

        if ($stripeAccount) {
            // Atualiza os dados do Stripe do usuário autenticado
            $stripeAccount->update([
                'stripe_public_key' => $request->stripe_public_key,
                'stripe_secret_key' => $request->stripe_secret_key,
                'stripe_webhook_secret' => $request->stripe_webhook_secret,
            ]);
        } else {
            // Cria uma nova conta Stripe para o usuário autenticado
            StripeAccount::create([
                'user_id' => $user->id,
                'stripe_public_key' => $request->stripe_public_key,
                'stripe_secret_key' => $request->stripe_secret_key,
                'stripe_webhook_secret' => $request->stripe_webhook_secret,
            ]);
        }

        return redirect()->back()->with('success', 'Conta Stripe atualizada com sucesso.');
    }
}
