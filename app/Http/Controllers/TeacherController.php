<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Subject;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
   public function index()
    {
        return Teacher::with('subjects', 'classes')->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:200',
            'subject' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        $user = User::create([
            'username' => strtolower(str_replace(' ', '_', $validated['full_name'])) . rand(1000, 9999),
            'password' => bcrypt('default123'),
            'role' => 'teacher',
        ]);

        $teacher = Teacher::create([
            ...$validated,
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Teacher created', 'teacher' => $teacher]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:200',
            'subject' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        $teacher = Teacher::findOrFail($id);
        $teacher->update($validated);

        return response()->json(['message' => 'Teacher updated', 'teacher' => $teacher]);
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted']);
    }

    public function assignSubject(Request $request, $teacherId)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        $subject->teacher_id = $teacherId;
        $subject->save();

        return response()->json(['message' => 'Subject assigned to teacher']);
    }

public function showProfile(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $teacher = Teacher::with(['subjects', 'classes'])
        ->where('user_id', $user->id)
        ->first();

    if (!$teacher) {
        return response()->json(['error' => 'Teacher profile not found'], 404);
    }

    return response()->json([
        'full_name' => $teacher->full_name,
        'email' => $teacher->email,
        'phone' => $teacher->phone,
    ]);
}


public function updateProfile(Request $request)
{
    try {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'nullable|string',
        ]);

        $teacher->update($request->only(['full_name', 'email', 'phone']));

        return response()->json(['message' => 'Profile updated', 'teacher' => $teacher]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Update failed',
            'message' => $e->getMessage(),
        ], 500);
    }
}



public function getTimetable(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'teacher') {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    $teacher = Teacher::with(['classes.subjects'])->where('user_id', $user->id)->first();

    if (!$teacher) {
        return response()->json(['error' => 'Teacher not found'], 404);
    }

    return response()->json([
        'timetable' => $teacher->classes->map(function ($class) {
            return [
                'id' => $class->id,
                'name' => $class->name,
                'subjects' => $class->subjects->map(fn($s) => ['name' => $s->name]),
            ];
        }),
    ]);
}


public function timetable(Request $request)
{
    $teacherId = $request->user()->id; // get authenticated user's ID
    $classes = ClassModel::with('subjects')
        ->where('teacher_id', $teacherId)
        ->get();

    return response()->json(['timetable' => $classes]);
}

public function getTeacherClasses()
{
    $user = Auth::user();
    $teacher = $user->teacher;

    if (!$teacher) {
        Log::error("Teacher profile not found for user ID: {$user->id}");
        return response()->json(['error' => 'Teacher profile not found'], 403);
    }

    $classes = ClassModel::with('students.user')
        ->where('teacher_id', $teacher->id)
        ->get();

    return response()->json(['classes' => $classes]);
}


}
