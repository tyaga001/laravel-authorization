<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private $permitKey;
    private $projectId;
    private $envId;
    public $teacher_role_id;
    private $client;

    public function __construct()
    {
        $this->permitKey = config('permit.key');
        $this->projectId = config('permit.project_id');
        $this->envId = config('permit.env_id');
        $this->teacher_role_id = config('permit.teacher_role_id');
        $this->client = new Client();
    }

    public function index(Request $request)
    {
        $students = Student::all();
        $user = auth()->user();

        if (!$this->checkPermission($user->email, 'read', 'student')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json($students, 200);
    }

    public function store(StoreStudentRequest $request)
    {
        $user = auth()->user();

        if (!$this->checkPermission($user->email, 'create', 'student')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $student = Student::create($request->validated());

        return response()->json($student, 201);
    }

    public function show(Student $student)
    {
        $user = auth()->user();

        if (!$this->checkPermission($user->email, 'read', 'student')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json($student, 200);
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $user = auth()->user();

        if (!$this->checkPermission($user->email, 'update', 'student')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $student->update($request->validated());

        return response()->json($student, 200);
    }

    public function destroy(Student $student)
    {
        $user = auth()->user();

        if (!$this->checkPermission($user->email, 'delete', 'student')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $student->delete();

        return response()->json(null, 204);
    }

    private function checkPermission($email, $action, $resource)
    {
        $url = "http://localhost:7766/allowed";
        $payload = [
            "user" => $email,
            "action" => $action,
            "resource" => [
                "type" => $resource,
                "tenant" => $this->projectId, // Replace with appropriate tenant ID if different
            ],
            "context" => new \stdClass(), // Empty context object
        ];

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$this->permitKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return $result
        } catch (\Exception $e) {
            return false;
        }
    }


}
