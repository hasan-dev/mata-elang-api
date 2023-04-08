<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OrganizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OrganizationController extends Controller
{
    //
    private $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function profile($id) {
        $data = auth()->user();
        return $this->organizationService->profile($id, $data);
    }

    public function get_sensor($id) {
        return $this->organizationService->get_sensor($id);
    }

    public function get_user($id) {
        return $this->organizationService->get_user($id);
    }

    public function edit(Request $request, $id) {
        return $this->organizationService->edit($id, $request->all());
    }

    public function create(Request $request) {
        return $this->organizationService->create($request->all());
    }

    public function get_role($id) {
        return $this->organizationService->get_role($id);
    }

    public function get_asset($id) {
        return $this->organizationService->get_asset($id);
    }

    public function register_role(Request $request, $id) {
        return $this->organizationService->register_role($id, $request->all());
    }

    public function edit_role(Request $request, $id) {
        return $this->organizationService->edit_role($id, $request->all());
    }
}
