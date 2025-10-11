<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
public function apply(Request $request)
{
    $validated = $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'required|string|max:500',
    ]);

    $user = Auth::user();
    $student = $user->student; // assumes User hasOne Student

    if (!$student) {
        return response()->json(['error' => 'Student profile not found'], 403);
    }

    $subject = Subject::find($validated['subject_id']);
    if (!$subject || !$subject->teacher_id) {
        return response()->json(['error' => 'Invalid subject or missing teacher'], 422);
    }

    $leave = LeaveRequest::create([
        'student_id' => $student->id,
        'subject_id' => $validated['subject_id'],
        'teacher_id' => $subject->teacher_id,
        'start_date' => $validated['start_date'],
        'end_date' => $validated['end_date'],
        'reason' => $validated['reason'],
        'status' => 'pending',
    ]);

    return response()->json(['message' => 'Leave request submitted', 'leave' => $leave]);
}

public function studentRequests()
{
    $user = Auth::user();
    $student = $user->student;

    if (!$student) {
        Log::error("Student profile not found for user ID: {$user->id}");
        return response()->json(['error' => 'Student profile not found'], 403);
    }

    $requests = LeaveRequest::with(['subject', 'teacher'])
        ->where('student_id', $student->id)
        ->orderByDesc('created_at')
        ->get();

    return response()->json(['requests' => $requests]);
}


public function RequestsforTeacher(Request $request)
{
    $user = Auth::user();
    $teacher = $user->teacher;

    if (!$teacher) {
        return response()->json(['error' => 'Teacher profile not found'], 403);
    }

    // ✅ Handle status update if PATCH request
    if ($request->isMethod('patch')) {
        $validated = $request->validate([
            'leave_id' => 'required|exists:leave_requests,id',
            'status' => 'required|in:approved,rejected',
        ]);

        $leave = LeaveRequest::where('id', $validated['leave_id'])
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$leave) {
            return response()->json(['error' => 'Leave request not found or unauthorized'], 404);
        }

        $leave->status = $validated['status'];
        $leave->save();

        return response()->json(['message' => 'Leave status updated', 'leave' => $leave]);
    }

    // ✅ Otherwise, return all leave requests for this teacher
    $requests = LeaveRequest::with(['student.user', 'subject'])
        ->where('teacher_id', $teacher->id)
        ->orderByDesc('created_at')
        ->get();

    return response()->json(['requests' => $requests]);
}


public function allRequests()
{
    $requests = LeaveRequest::with(['student', 'subject', 'teacher'])
        ->orderByDesc('created_at')
        ->get();

    return response()->json(['requests' => $requests]);
}

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
