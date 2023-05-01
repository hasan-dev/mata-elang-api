<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

    class SensorService
    {
        private $baseUri;
        
        public function __construct()
        {
            $this->baseUri = config('services.sensors.base_uri');
        }
        
        public function registerSensor($data) {
            return Http::post($this->baseUri . '/register', $data);
        }

        public function editSensor($data, $id) {
            return Http::patch($this->baseUri . '/update/' . $id, $data);
        }

        // public function heartbeat($data) {
        //     return Http::post($this->baseUri . '/heartbeat', $data);
        // }

        public function login($data) {
            return Http::post($this->baseUri . '/login', $data);
        }

        public function delete($id) {
            return Http::delete($this->baseUri . '/delete/' . $id);
        }

        public function detail($id) {
            return Http::get($this->baseUri . '/' . $id);
        }
    }
