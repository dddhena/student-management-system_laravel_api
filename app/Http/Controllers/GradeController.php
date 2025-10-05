<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

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

public function studentGrades($studentId)
{
    $student = Student::with(['grades.subject', 'grades.teacher'])->findOrFail($studentId);

    return response()->json([
        'student' => $student->full_name,
        'grades' => $student->grades->map(function ($grade) {
            return [
                'subject' => $grade->subject->name,
                'exam_type' => $grade->exam_type,
                'score' => $grade->score,
                'teacher' => $grade->teacher ? $grade->teacher->full_name : 'N/A',
                'updated_at' => $grade->updated_at->format('Y-m-d'),
            ];
        }),
    ]);
}
}
