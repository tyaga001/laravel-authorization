<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use GuzzleHttp\Client;

class StudentController extends Controller
{
    private $permitKey;
    private $projectId;
    private $envId;

    public function __construct()
    {
        $this->permitKey = config('permit.key');
        $this->projectId = config('permit.project_id');
        $this->envId = config('permit.env_id');
    }

    public function index()
    {
        $students = Student::all();
        $user = auth()->user();

        if (!$this->checkRole($user->email, 'teacher')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json($students, 200);
    }

    public function store(StoreStudentRequest $request)
    {
        $student = Student::create($request->validated());
        $user = auth()->user();

        if (!$this->checkRole($user->email, 'teacher')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json($student, 201);
    }

    public function show(Student $student)
    {
        $user = auth()->user();

        if (!$this->checkRole($user->email, 'teacher')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json($student, 200);
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $user = auth()->user();

        if (!$this->checkRole($user->email, 'teacher')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $student->update($request->validated());
        return response()->json($student, 200);
    }

    public function destroy(Student $student)
    {
        $user = auth()->user();

        if (!$this->checkRole($user->email, 'teacher')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $student->delete();
        return response()->json(null, 204);
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
