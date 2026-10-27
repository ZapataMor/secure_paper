<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentBillingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'client')->whereNull('deleted_at')],
            'country' => ['required', 'regex:/^[A-Z]{2}$/D'],
            'tax' => ['required', Rule::in(['1900', '0'])], 'export_verified' => ['sometimes', 'boolean'],
        ]);
        if ((int) $data['tax'] === 0 && ($data['country'] === 'CO' || empty($data['export_verified']))) {
            throw ValidationException::withMessages(['tax' => 'Verifica y conserva los soportes de exportación antes de aplicar IVA 0 %.']);
        }
        $user = User::findOrFail($data['user_id']);
        $user->forceFill(['payment_billing_country' => $data['country'], 'payment_tax_basis_points' => (int) $data['tax'],
            'payment_export_verified' => (int) $data['tax'] === 0, 'payment_billing_verified_at' => now()])->save();

        return redirect()->route('admin.payment-quotes')->with('status', 'Datos fiscales verificados. El cliente puede comprar las opciones disponibles.');
    }
}
