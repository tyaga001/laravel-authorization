<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use GuzzleHttp\Client;

class TeacherController extends Controller
{
    private $permitKey;
    private $projectId;
    private $envId;
    private $roleId;

    public function __construct()
    {
        $this->permitKey = config('permit.key');
        $this->projectId = config('permit.project_id');
        $this->envId = config('permit.env_id');
        $this->roleId = config('permit.teacher_role_id');
    }

    public function store(StoreTeacherRequest $request)
    {
        $teacher = Teacher::create($request->validated());
        $user = auth()->user();

        if (!$this->checkRole($user->email, 'admin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $this->createAccessRequest($teacher);

        return response()->json($teacher, 201);
    }

    private function createAccessRequest($teacher)
    {
        $client = new Client();
        $url = "https://api.permit.io/v2/facts/{$this->projectId}/{$this->envId}/access_requests";

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$this->permitKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'access_request_details' => [
                        'tenant' => 'default',
                        'resource' => 'student',
                        'resource_instance' => $teacher->id,
                        'role' => $this->roleId,
                    ],
                    'reason' => 'done onboarding',
                ],
            ]);

            if ($response->getStatusCode() == 200) {
                // Log success or handle response if needed
                return true;
            } else {
                return false;
                // Log error or handle response if needed
            }
        } catch (\Exception $e) {
            return false;
            // Handle exception or log error
        }
    }

    private function checkRole($email, $roleKey)
    {
        $client = new Client();
        $url = "https://api.permit.io/v2/facts/{$this->projectId}/{$this->envId}/roles";

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => "Bearer {$this->permitKey}",
                    'Content-Type' => 'application/json',
                ],
            ]);

            $roles = json_decode($response->getBody()->getContents(), true);

            foreach ($roles['data'] as $role) {
                if ($role['key'] === $roleKey) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
