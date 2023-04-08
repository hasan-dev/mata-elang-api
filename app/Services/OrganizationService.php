<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

    class OrganizationService
    {
        private $baseUri;
        
        public function __construct()
        {
            $this->baseUri = config('services.organizations.base_uri');
        }
        
        public function profile($id, $data) {
            return Http::post($this->baseUri .'/'. $id, $data);
        }

        public function create($data) {
            return Http::post($this->baseUri . '/create', $data);
        }

        public function get_sensor($id) {
            return Http::get($this->baseUri . '/' . $id . '/sensors/all');
        }

        public function get_user($id) {
            return Http::get($this->baseUri . '/' . $id . '/users/all');
        }

        public function edit($id, $data) {
            return Http::patch($this->baseUri . '/update/' . $id, $data);
        }

        public function get_role($id) {
            return Http::get($this->baseUri . '/' . $id . '/roles/all');
        }

        public function get_asset($id) {
            return Http::get($this->baseUri . '/' . $id . '/assets/all');
        }

        public function register_role($id, $data) {
            return Http::post($this->baseUri . '/' . $id . '/users/register-role', $data);
        }

        public function edit_role($id, $data) {
            return Http::post($this->baseUri . '/' . $id . '/users/edit_role', $data);
        }
    }
