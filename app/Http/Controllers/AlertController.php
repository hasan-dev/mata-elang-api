<?php

namespace App\Http\Controllers;

use App\Models\Permission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\SensorResource;
use App\Services\SensorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;




class AlertController extends Controller
{
    protected $sensorService;
    public function __construct(SensorService $sensorService)
    {
        $this->sensorService = $sensorService;
        $this->middleware('auth:api', ['except' => [
            // 'getData',
        ]]);
    }

    public function getData()
    {

        try {
            $user = auth('api')->user();


            $userId = $user['id'];

            $data = DB::table('sensors')
                ->join('organizations', 'sensors.organization_id', '=', 'organizations.id')
                ->select('sensors.name')
                ->where('sensors.organization_id', $userId)
                ->where('sensors.status', '!=', 'deleted')
                ->get();

            $names = $data->pluck('name')->toArray();

            $body = [
                'query' => [
                    'match_all' => new \stdClass()
                ],
                'size' => 1000,
                'sort' => [
                    ['@timestamp' => ['order' => 'desc']]
                ]
            ];
            $result = Http::withBasicAuth(env('OPENSEARCH_USER'), env('OPENSEARCH_PASSWORD'))->withoutVerifying()->post(env('OPENSEARCH_API_URL') . '/alert/_search?filter_path=hits.hits._source', $body);

            if ($result->successful()) {
                $alerts = $result->json();

                $filteredAlerts = array_filter($alerts['hits']['hits'], function ($alert) use ($names) {
                    return isset($alert['_source']['sensor_id']) && in_array($alert['_source']['sensor_id'], $names);
                });


                if (!empty($filteredAlerts)) {
                    return response()->json(
                        array_values($filteredAlerts)
                    );
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No data found matching the sensor ID'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to get alert data'
                ], $result->status());
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error occurred' . $e->getMessage()
            ], 500);
        }
    }

    public function showAlert()
    {
        // Retrieve user data from session
        $data = session('user_data');

        // Check if data exists
        if ($data) {
            // Logic using $data
            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => $data
            ]);
        }
    }
}
