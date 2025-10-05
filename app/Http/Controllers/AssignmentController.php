<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
    $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'due_date' => 'required|date',
        'pdf' => 'nullable|file|mimes:pdf|max:10240',
    ]);

    $pdfPath = $request->hasFile('pdf')
        ? $request->file('pdf')->store('assignments', 'public')
        : null;

    $assignment = Assignment::create([
        'subject_id' => $request->subject_id,
        'title' => $request->title,
        'description' => $request->description,
        'due_date' => $request->due_date,
        'pdf_path' => $pdfPath,
    ]);

    return response()->json(['message' => 'Assignment created', 'assignment' => $assignment]);
}


public function submit(Request $request)
{
    try {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $assignment = Assignment::with('subject')->findOrFail($request->assignment_id);
        $teacherId = $assignment->subject->teacher_id ?? null;

        $path = $request->file('file')->store('submissions', 'public');

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'teacher_id' => $teacherId,
            'file_url' => $path,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Submission uploaded successfully',
            'submission' => $submission
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Submission failed',
            'details' => $e->getMessage()
        ], 500);
    }
}

public function submitAssignment(Request $request)
{
    $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'due_date' => 'required|date',
        'pdf' => 'nullable|file|mimes:pdf|max:10240',
    ]);

    $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

    $pdfPath = $request->hasFile('pdf')
        ? $request->file('pdf')->store('assignments', 'public')
        : null;

    $assignment = Assignment::create([
        'subject_id' => $request->subject_id,
        'teacher_id' => $teacher->id, // ✅ FIXED
        'title' => $request->title,
        'description' => $request->description,
        'due_date' => $request->due_date,
        'pdf_path' => $pdfPath,
    ]);

    return response()->json([
        'message' => 'Assignment uploaded successfully',
        'assignment' => $assignment
    ]);
}

public function grade(Request $request, $submissionId)
{
    $request->validate([
        'grade' => 'required|numeric|min:0|max:100',
        'feedback' => 'nullable|string',
    ]);

    $submission = Submission::findOrFail($submissionId);
    $submission->update([
        'grade' => $request->grade,
        'feedback' => $request->feedback,
    ]);

    return response()->json(['message' => 'Submission graded']);
}


public function assignedToStudent()
{
    $student = Student::where('user_id', Auth::id())->firstOrFail();

    $assignments = Assignment::with('subject')
        ->whereHas('subject', function ($query) use ($student) {
            $query->where('class_id', $student->class_id);
        })
        ->orderByDesc('due_date')
        ->get()
        ->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'due_date' => $assignment->due_date,
                'pdf_path' => $assignment->pdf_path,
                'subject_name' => $assignment->subject->name,
            ];
        });

    return response()->json($assignments);
}

public function teacherAssignments()
{
    $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

    $assignments = Assignment::with('subject')
        ->whereHas('subject', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->orderByDesc('due_date')
        ->get()
        ->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'due_date' => $assignment->due_date,
                'pdf_path' => $assignment->pdf_path,
                'subject_id' => $assignment->subject_id,
                'subject_name' => $assignment->subject->name,
            ];
        });

    return response()->json($assignments);
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
   public function update(Request $request, $id)
{
    $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'due_date' => 'required|date',
        'pdf' => 'nullable|file|mimes:pdf|max:10240',
    ]);

    $assignment = Assignment::findOrFail($id);

    if ($request->hasFile('pdf')) {
        $assignment->pdf_path = $request->file('pdf')->store('assignments', 'public');
    }

    $assignment->update([
        'subject_id' => $request->subject_id,
        'title' => $request->title,
        'description' => $request->description,
        'due_date' => $request->due_date,
    ]);

    return response()->json(['message' => 'Assignment updated', 'assignment' => $assignment]);
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $assignment = Assignment::findOrFail($id);

    // Optional: check if teacher owns the subject
    // $this->authorize('delete', $assignment);

    $assignment->delete();

    return response()->json(['message' => 'Assignment deleted']);
}

}
