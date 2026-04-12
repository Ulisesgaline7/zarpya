<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdCreditService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class AdCreditAdminController extends Controller
{
    public function __construct(protected AdCreditService $creditService) {}

    public function add(Request $request)
    {
        $request->validate([
            'store_id'    => 'required|integer|exists:stores,id',
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $this->creditService->addCredits(
            storeId:     $request->store_id,
            amount:      (float) $request->amount,
            reference:   'admin-manual',
            description: $request->description ?? 'Recarga manual por administrador',
        );

        Toastr::success('Créditos agregados correctamente.');
        return back();
    }
}
