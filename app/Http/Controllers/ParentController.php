<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ParentController extends Controller
{
  public function getChildrenOverview()
    {
        $user = Auth::user();
        $parent = $user->parent; // Adjust if your model is named Guardian

        if (!$parent) {
            
            return response()->json(['error' => 'Parent profile not found'], 403);
        }

        $children = Student::with(['user', 'grades.subject', 'attendance']) // ✅ fixed relationship name
            ->where('guardian_id', $parent->id) // ✅ using guardian_id as intended
            ->get();

        return response()->json(['children' => $children]);
    }

}
