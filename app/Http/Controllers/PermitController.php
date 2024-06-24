<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PermitController extends Controller
{
    private $client;
    private $apiKey;
    private $projectId;
    private $envId;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('PERMIT_API_SECRET_KEY');
        $this->projectId = env('PERMIT_PROJECT_ID');
        $this->envId = env('PERMIT_ENV_ID');
    }

    public function createRole(Request $request)
    {
        $url = "https://cloudpdp.api.permit.io/v2/schema/{$this->projectId}/{$this->envId}/roles";

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => $request->all(),
        ]);

        return response()->json(json_decode($response->getBody()->getContents()), $response->getStatusCode());
    }

    public function createResource(Request $request)
    {
        $url = "https://cloudpdp.api.permit.io/v2/schema/{$this->projectId}/{$this->envId}/resources";

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => $request->all(),
        ]);

        return response()->json(json_decode($response->getBody()->getContents()), $response->getStatusCode());
    }
}
