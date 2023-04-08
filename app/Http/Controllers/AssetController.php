<?php

namespace App\Http\Controllers;

use App\Services\AssetService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    //
    private $assetservice;

    public function __construct(AssetService $assetservice)
    {
        $this->assetservice = $assetservice;
        $this->middleware('auth:api');
    }

    public function register(Request $request) {
        return $this->assetservice->register($request->all());
    }

    public function edit(Request $request, $id) {
        return $this->assetservice->edit($request->all(), $id);
    }

    public function detail($id) {
        return $this->assetservice->detail($id);
    }
}
