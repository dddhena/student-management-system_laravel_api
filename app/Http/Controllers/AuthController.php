<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function login(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = User::where('username', $request->username)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    $user->tokens()->delete(); // ✅ revoke old tokens

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'name' => $user->name,
        ]
    ]);
}


public function logout(Request $request)
{
    $user = $request->user();

    // Revoke all tokens for this user
    $user->tokens()->delete();

    return response()->json(['message' => 'Logged out successfully']);
}

}

