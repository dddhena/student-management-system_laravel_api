<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use Illuminate\Support\Facades\Log;

class TimetableController extends Controller
{

public function store(Request $request)
{
    $validated = $request->validate([
        'teacher_id' => 'required|exists:teachers,id',
        'class_id' => 'required|exists:classes,id',
        'subject_id' => 'required|exists:subjects,id',
        'day' => 'required|string',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'room' => 'nullable|string',
    ]);

    $entry = Timetable::create($validated);

    return response()->json([
        'message' => 'Timetable entry created successfully',
        'timetable' => $entry
    ], 201);
}


    public function index(Request $request)
{
    $user = $request->user(); // or auth()->user()
    Log::info('Authenticated user:', ['id' => $user->id]);

    $timetable = Timetable::with(['class', 'subject'])
        ->where('teacher_id', $user->id)
        ->orderBy('day')
        ->get();

    Log::info('Fetched timetable:', $timetable->toArray());

    return response()->json(['timetable' => $timetable]);
}

public function adminIndex()
{
    $timetable = Timetable::with(['teacher', 'class', 'subject'])
        ->orderBy('day')
        ->get();

    return response()->json(['timetable' => $timetable]);
}

public function teacherTimetable(Request $request)
{
    $user = $request->user();
    $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

    if (!$teacher) {
        return response()->json(['error' => 'No teacher profile found for this user'], 404);
    }

    $timetable = Timetable::with(['class', 'subject'])
        ->where('teacher_id', $teacher->id)
        ->orderBy('day')
        ->get();

    return response()->json(['timetable' => $timetable]);
}



}
