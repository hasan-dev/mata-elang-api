<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Services\SensorService;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SensorController extends Controller
{
    //
    private $sensorService;

    public function __construct(SensorService $sensorService)
    {
        $this->sensorService = $sensorService;
        $this->middleware('auth:api', ['except' => ['login', 'updateStatus']]);
        // $this->middleware('auth:sensors', ['only' => ['heartbeat']]);
    }

    public function register(Request $request) {
        return $this->sensorService->registerSensor($request->all());
    }

    public function edit(Request $request, $id) {
        return $this->sensorService->editSensor($request->all(), $id);
    }

    public function login(Request $request) {
        return $this->sensorService->login($request->all());
    }

    public function delete($id) {
        return $this->sensorService->delete($id);
    }

    public function detail($id) {
        return $this->sensorService->detail($id);
    }

    public function updateStatus(Request $request, $id) {
        return $this->sensorService->updateStatus($request->all(), $id);
    }

    // public function heartbeat(Request $request) {
    //     return $this->sensorService->heartbeat($request->all());
    // }
}
