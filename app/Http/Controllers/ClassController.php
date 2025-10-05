<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassModel;

class ClassController extends Controller
{
   
public function index()
{
   return response()->json(ClassModel::all());

}


public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'teacher_id' => 'nullable|exists:teachers,id',
    ]);

    $class = ClassModel::create($validated);

    return response()->json(['message' => 'Class created', 'class' => $class]);
}
public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $class->update($request->all());
        return $class;
    }

    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        $class->delete();
        return response()->json(['message' => 'Class deleted']);
    }
}
