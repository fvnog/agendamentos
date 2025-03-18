<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PixAccount;
use Illuminate\Support\Facades\Auth;

class PixAccountController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $pixAccount = PixAccount::where('user_id', $user->id)->first(); // Buscar a conta Pix do usuário autenticado

        return view('admin.pix_account.edit', compact('pixAccount'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'pix_key' => 'required|string',
            'pix_key_type' => 'required|string|in:cpf,cnpj,email,telefone,aleatoria',
            'bb_client_id' => 'nullable|string',
            'bb_client_secret' => 'nullable|string',
            'bb_gw_app_key' => 'nullable|string',
        ]);

        $user = Auth::user();
        $pixAccount = PixAccount::where('user_id', $user->id)->first();

        $data = [
            'bank_name' => $request->bank_name,
            'pix_key' => $request->pix_key,
            'pix_key_type' => $request->pix_key_type,
        ];

        // 🔹 Se o banco for "Banco do Brasil", adiciona os campos extras
        if ($request->bank_name === "Banco do Brasil") {
            $data['bb_client_id'] = $request->bb_client_id;
            $data['bb_client_secret'] = $request->bb_client_secret;
            $data['bb_gw_app_key'] = $request->bb_gw_app_key;
        } else {
            // 🔹 Se for outro banco, limpa os campos do Banco do Brasil para evitar dados desnecessários
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

        return redirect()->back()->with('success', 'Conta Pix atualizada com sucesso.');
    }
}
