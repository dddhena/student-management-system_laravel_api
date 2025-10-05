<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Attendance;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    // ✅ Subjects assigned to the logged-in teacher
public function teacherSubjects()
{
    $userId = Auth::id(); // this is users.id
    $teacher = Teacher::where('user_id', $userId)->first();

    if (!$teacher) {
        return response()->json(['error' => 'Teacher not found'], 404);
    }

    $subjects = Subject::where('teacher_id', $teacher->id)
        ->with('class')
        ->select('id', 'name', 'class_id')
        ->get();

    return response()->json($subjects);
}



public function studentsForSubject($subjectId)
{
    $subject = Subject::with('class')->findOrFail($subjectId);

    if (!$subject->class) {
        return response()->json(['error' => 'Class not found for this subject'], 404);
    }

    $classId = $subject->class->id;

    // ✅ Fetch all students and filter by class_id
    $students = Student::where('class_id', $classId)->get();

    if ($students->isEmpty()) {
        return response()->json(['error' => 'No students found in this class'], 404);
    }

    return response()->json([
        'subject' => $subject->name,
        'class' => $subject->class->name,
        'students' => $students,
    ]);
}





public function storeBatch(Request $request)
{
    Log::info('Incoming attendance payload:', $request->all());

    $validated = $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'date' => 'required|date',
        'records' => 'required|array|min:1',
        'records.*.student_id' => 'required|exists:students,id',
        'records.*.status' => 'required|in:present,absent,late',
    ]);

    // ✅ Resolve teacher ID safely
    $userId = Auth::id();
    $teacher = Teacher::where('user_id', $userId)->first();

    if (!$teacher) {
        Log::error("Teacher not found for user ID: $userId");
        return response()->json(['error' => 'Teacher not found'], 403);
    }

    $teacherId = $teacher->id;

    try {
        foreach ($validated['records'] as $record) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $record['student_id'],
                    'subject_id' => $validated['subject_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $record['status'],
                    'teacher_id' => $teacherId,
                ]
            );
        }

        Log::info("Attendance recorded successfully for subject {$validated['subject_id']} on {$validated['date']}");
        return response()->json(['message' => 'Attendance recorded']);
    } catch (\Exception $e) {
        Log::error('Attendance insert failed: ' . $e->getMessage());
        return response()->json(['error' => 'Insert failed', 'details' => $e->getMessage()], 500);
    }
}
public function summary(Request $request)
{
    $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'date' => 'required|date',
    ]);

    $records = Attendance::with('student.user')
        ->where('subject_id', $request->subject_id)
        ->where('date', $request->date)
        ->get();

    return response()->json($records);
}

public function getStudentAttendances($studentId)
{
    $student = Student::with('user')->findOrFail($studentId);

    $attendances = Attendance::with(['subject', 'teacher'])
        ->where('student_id', $studentId)
        ->orderByDesc('date')
        ->get()
        ->map(function ($record) {
            return [
                'date' => $record->date,
                'status' => $record->status,
                'subject' => $record->subject->name ?? '—',
                'teacher' => $record->teacher->user->name ?? '—',
            ];
        });

    return response()->json([
        'student' => $student->user->name,
        'class' => $student->class->name ?? '—',
        'records' => $attendances,
    ]);
}

public function studentAttendances()
{
    $student = Student::where('user_id', Auth::id())->firstOrFail();

    $records = Attendance::with(['subject', 'teacher.user'])
        ->where('student_id', $student->id)
        ->orderByDesc('date')
        ->get()
        ->map(function ($record) {
            return [
                'date' => $record->date,
                'status' => $record->status,
                'subject' => $record->subject->name ?? '—',
                'teacher' => $record->teacher->user->name ?? '—',
            ];
        });

    return response()->json($records);
}

public function getAllStudentAttendances()
{
    $students = Student::with(['user', 'class', 'attendances.subject', 'attendances.teacher'])->get();

    $data = $students->map(function ($student) {
        return [
            'student_id' => $student->id,
            'name' => $student->user->name,
            'class' => $student->class->name ?? '—',
            'attendances' => $student->attendances->map(function ($record) {
                return [
                    'date' => $record->date,
                    'status' => $record->status,
                    'subject' => $record->subject->name ?? '—',
                    'teacher' => $record->teacher->user->name ?? '—',
                ];
            }),
        ];
    });

    return response()->json($data);
}


}
