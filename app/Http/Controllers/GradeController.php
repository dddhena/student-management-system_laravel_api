<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
   public function storeGrade(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'subject_id' => 'required|exists:subjects,id',
        'exam_type' => 'required|in:quiz,midterm,final',
        'score' => 'nullable|numeric|min:0|max:100',
    ]);

    $userId = Auth::id();
    $teacher = Teacher::where('user_id', $userId)->first();

    if (!$teacher) {
        return response()->json(['error' => 'Teacher not found'], 403);
    }

    $grade = Grade::updateOrCreate(
        [
            'student_id' => $request->student_id,
            'subject_id' => $request->subject_id,
            'exam_type' => $request->exam_type,
        ],
        [
            'score' => $request->score,
            'teacher_id' => $teacher->id,
        ]
    );

    return response()->json(['message' => 'Grade recorded', 'grade' => $grade]);
}



public function studentGrades(Request $request, $id)
{
    $user = $request->user();
    $student = Student::with('user')->find($id);

    // Validate student existence and role
    if (!$student || $student->user->role !== 'student') {
        Log::error("Student not found or invalid role for ID: $id");
        return response()->json(['error' => 'Student not found'], 404);
    }

    // Authorization: students can only view their own grades
    if ($user->role === 'student' && $student->user_id !== $user->id) {
        Log::warning("Unauthorized access: Student {$user->id} tried to access grades for Student {$id}");
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Fetch grades with subject and teacher info
    $grades = Grade::with(['subject', 'teacher'])
        ->where('student_id', $id)
        ->orderByDesc('updated_at')
        ->get()
        ->map(function ($grade) {
            return [
                'subject' => $grade->subject->name ?? '—',
                'exam_type' => $grade->exam_type,
                'score' => $grade->score,
                'teacher' => $grade->teacher->full_name ?? '—',
                'updated_at' => $grade->updated_at->format('Y-m-d'),
            ];
        });

    return response()->json([
        'student' => $student->user->full_name ?? $student->full_name,
        'grades' => $grades,
    ]);
}


}
