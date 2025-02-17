<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuposCentrosReclutamientoRequest;
use App\Http\Requests\UpdateCuposCentrosReclutamientoRequest;
use App\Models\Apertura;
use App\Models\CuposCentrosReclutamiento;
use App\Models\CuposDivision;
use Illuminate\Http\Request;

class CuposCentrosReclutamientoController extends Controller
{
    private function successResponse($data, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }
    private function errorResponse($error, $message = 'Something went wrong!', $status = 400)
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message,
        ], $status);
    }
    public function index()
    {
        $cupos_centros_reclutamiento = CuposCentrosReclutamiento::all();

        if ($cupos_centros_reclutamiento->isEmpty()) {
            return $this->errorResponse(null, 'Cupos centros reclutamiento is empty.', 404);
        }

        return $this->successResponse($cupos_centros_reclutamiento, 'Cupos centros reclutamiento retrieved successfully');
    }

    public function store(StoreCuposCentrosReclutamientoRequest $request)
    {
        try {
            // division validation cupos
            $total_cupos_division = CuposDivision::where('codigo_division', $request->codigo_division)
                ->where('gestion_apertura', $request->gestion)
                ->first();


            if (!$total_cupos_division) {
                return $this->errorResponse(null, 'No existen cupos asignados para la division de esta gestion');
            }

            ///sum of cupos from centros_reclutamiento
            $total_cupos_centro_reclutamiento_division =
                CuposCentrosReclutamiento::where('codigo_division', $request->codigo_division)
                ->where('gestion', $request->gestion)
                ->sum('cupo');


            // division cupos vs sum of cupos from centros_reclutamiento | validation
            if ($total_cupos_centro_reclutamiento_division + $request->cupo > $total_cupos_division->cupos) {
                return $this->errorResponse(null, 'La cantidad de cupos excede a la cantidad de cupos asignada para esta division a la cual pertenece este centro de reclutamiento');
            }


            $cupo_centros_reclutamiento = CuposCentrosReclutamiento::create($request->all());

            return $this->successResponse($cupo_centros_reclutamiento, 'Cupos centros reclutamiento created succesfully.');
        } catch (\Exception $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
    public function show(string $id)
    {
        try {
            $cupos_centros_reclutamiento = CuposCentrosReclutamiento::findOrFail($id);


            return $this->successResponse($cupos_centros_reclutamiento, 'Cupos de la unidad educativa deleted successfully.');
        } catch (\Exception $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
    public function udpate(UpdateCuposCentrosReclutamientoRequest $request, string $id)
    {
        try {

            $total_cupos_division = CuposDivision::where('codigo_division', $request->codigo_division)
                ->where('gestion_apertura', $request->gestion)
                ->first()
                ->cupos;

            $total_cupos_centro_reclutamiento_division =
                CuposCentrosReclutamiento::where('id_centros_reclutamiento', $id)
                ->where('codigo_division', $request->codigo_division)
                ->where('gestion', $request->gestion)
                ->sum('cupo');

            if ($total_cupos_centro_reclutamiento_division + $request->cupo > $total_cupos_division) {
                return $this->errorResponse(null, 'La cantidad de cupos excede a la cantidad de cupos asignada para esta division de este centro de reclutamiento');
            }



            $cupos_centros_reclutamiento = CuposCentrosReclutamiento
                ::firstWhere('id', $id)
                ->where('gestion', $request->gestion)
                ->update($request->validated());


            return $this->successResponse($cupos_centros_reclutamiento, 'Actualizado correctamente!');
        } catch (\Exception $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
    public function destroy(string $id)
    {
        try {
            $cupos_centros_reclutamiento = CuposCentrosReclutamiento::findOrFail($id);

            $cupos_centros_reclutamiento->delete();

            return $this->successResponse($cupos_centros_reclutamiento, 'Cupos de la unidad educativa deleted successfully.');
        } catch (\Exception $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
