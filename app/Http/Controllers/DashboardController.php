<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = tenant();

        if (!$tenant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'message' => 'Berhasil mengakses dashboard tenant',
            'data' => [
                'some_metric' => rand(100, 1000),
                'another_metric' => rand(1000, 10000),
            ]
        ]);
    }
}
