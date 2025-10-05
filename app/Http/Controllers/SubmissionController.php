<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Submission;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */


   public function teacherSubmissions()
{
    try {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $submissions = Submission::with(['assignment.subject', 'student.user', 'grade'])
    ->where('teacher_id', $teacher->id)
    ->orderByDesc('submitted_at')
    ->get()
    ->map(function ($submission) {
        return [
            'id' => $submission->id,
            'assignment_title' => $submission->assignment->title ?? '—',
            'subject_name' => $submission->assignment->subject->name ?? '—',
            'student_name' => $submission->student->user->name ?? '—',
            'file_url' => $submission->file_url,
            'submitted_at' => $submission->submitted_at,
            'score' => $submission->grade->score ?? null,
            'feedback' => $submission->grade->feedback ?? null,
        ];
    });


        return response()->json($submissions);
    } catch (\Exception $e) {
        Log::error('❌ teacherSubmissions failed', ['error' => $e->getMessage()]);
        return response()->json([
            'error' => 'Failed to fetch submissions',
            'details' => $e->getMessage()
        ], 500);
    }
}

public function grade(Request $request, $id)
{
     $request->validate([
        'score' => 'nullable|numeric|min:0|max:100',
        'feedback' => 'nullable|string|max:1000',
    ]);

    $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
    $submission = Submission::with('assignment.subject')->findOrFail($id);

    if ($submission->assignment->subject->teacher_id !== $teacher->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $grade = Grade::updateOrCreate(
        ['submission_id' => $submission->id],
        [
            'student_id' => $submission->student_id,
            'subject_id' => $submission->assignment->subject_id,
            'exam_type' => 'assignment',
            'score' => $request->score,
            'feedback' => $request->feedback,
        ]
    );

    return response()->json([
        'message' => '✅ Grade stored in grades table',
        'grade' => $grade
    ]);
}

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
