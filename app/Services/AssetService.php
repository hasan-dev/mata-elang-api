<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AssetService
{
    private $baseUri;
    
    public function __construct()
    {
        $this->baseUri = config('services.assets.base_uri');
    }
    
    public function detail($id) {
        return Http::get($this->baseUri .'/'. $id);
    }

    public function edit($data, $id) {
        return Http::patch($this->baseUri.'/update/'.$id, $data);
    }

    public function register($data) {
        return Http::post($this->baseUri . '/register', $data);
    }

    public function delete($id) {
        return Http::delete($this->baseUri . '/delete/' . $id);
    }
}
