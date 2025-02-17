<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::all();

        if ($departamentos->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'departamentos is empty',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $departamentos,
            'message' => 'departamentos listed successfully',
        ]);
    }
}
