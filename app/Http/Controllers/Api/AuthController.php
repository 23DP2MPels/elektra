<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vards' => 'required|string|max:255',
            'epasts' => 'required|string|email|max:255|unique:lietotaji,epasts',
            'parole' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'vards' => $validated['vards'],
            'epasts' => $validated['epasts'],
            'parole' => Hash::make($validated['parole']),
            'loma' => 'registrets_lietotajs',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'User registered successfully'
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'epasts' => 'required|email',
            'parole' => 'required',
        ]);

        $user = User::where('epasts', $request->epasts)->first();

        if (!$user || !Hash::check($request->parole, $user->parole)) {
            throw ValidationException::withMessages([
                'epasts' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'vards' => 'sometimes|string|max:255',
            'epasts' => 'sometimes|email|max:255|unique:lietotaji,epasts,' . $user->lietotaja_id . ',lietotaja_id',
            'current_password' => 'required_with:parole',
            'parole' => 'nullable|string|min:8|confirmed',
            'sanemt_pazinojumus' => 'sometimes|boolean',
        ]);

        if (isset($validated['parole'])) {
            if (!Hash::check($validated['current_password'], $user->parole)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Current password is incorrect.'],
                ]);
            }
            $validated['parole'] = Hash::make($validated['parole']);
        }

        unset($validated['current_password']);
        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Profile updated successfully'
        ]);
    }
}
