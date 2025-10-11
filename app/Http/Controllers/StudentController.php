<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\Fee;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('class');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        Log::info('Admission attempt started', ['request' => $request->all()]);

        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:200',
                'gender' => 'required|in:male,female',
                'date_of_birth' => 'required|date',
                'class_id' => 'required|exists:classes,id',
            ]);

            Log::info('Validation passed', ['validated' => $validated]);

            $user = User::create([
                'name' => $validated['full_name'],
                'password' => bcrypt('default123'),
                'role' => 'student',
                'username' => strtolower(str_replace(' ', '_', $validated['full_name'])) . rand(1000, 9999),
            ]);

            Log::info('User created', ['user_id' => $user->id]);

            $student = Student::create([
                ...$validated,
                'user_id' => $user->id,
            ]);

            Log::info('Student created successfully', ['student' => $student]);

            return response()->json([
                'message' => 'Student admitted successfully',
                'student' => $student,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Admission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Admission failed'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:200',
            'gender' => 'sometimes|in:male,female',
            'date_of_birth' => 'sometimes|date',
            'guardian_id' => 'nullable|exists:parents,id',
            'class_id' => 'sometimes|exists:classes,id',
        ]);

        $student = Student::findOrFail($id);
        $student->update($validated);

        return response()->json(['message' => 'Student updated', 'student' => $student]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json(['message' => 'Student deleted']);
    }

  

public function mySubjects(Request $request)
{
    $user = $request->user();
    Log::info('Authenticated user:', ['id' => $user->id]);

    $student = \App\Models\Student::where('user_id', $user->id)->first();

    if (!$student) {
        Log::error('No student profile found for user ID: ' . $user->id);
        return response()->json(['error' => 'Student profile not found'], 404);
    }

    $subjects = \App\Models\Subject::where('class_id', $student->class_id)->get();

    Log::info('Fetched subjects:', $subjects->toArray());

    return response()->json(['subjects' => $subjects]);
}

public function getFee($id)
{
    $student = Student::findOrFail($id);
    $fee = Fee::where('student_id', $student->id)->first();

    return response()->json([
        'amount' => $fee->amount ?? 0
    ]);
}


}
