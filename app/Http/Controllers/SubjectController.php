<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 public function index()
{
    return Subject::with(['class', 'teacher'])->latest()->get();
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
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'class_id' => 'required|exists:classes,id',
        'teacher_id' => 'nullable|exists:teachers,id',
    ]);

    $subject = Subject::create($validated);

    return response()->json(['message' => 'Subject created', 'subject' => $subject]);
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
        $subject = Subject::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $subject->update($request->all());
        return $subject;
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return response()->json(['message' => 'Subject deleted']);
    }
}
