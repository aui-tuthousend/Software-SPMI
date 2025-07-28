<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\error;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request): JsonResponse
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            // return error($validator->errors());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->all();

        User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => $input['role'],
            'password' => bcrypt($input['password'])
        ]);

        return response()->json([
            'success' => 'true',
            'message' => 'Registrasi berhasil. Silahkan login!'
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Could not create token'], 500);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'token' => $token,
            'name' => $user->name,
            'userRole' => $user->role,
            'idk' => bin2hex(random_bytes(8))
        ]);
    }
    
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['success' => true, 'message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Failed to logout, please try again.'], 500);
        }
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function listUser(): JsonResponse
    {
        return response()->json(User::all());
    }

    public function editUserRole(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'new_role' => 'required|string|in:Pelaksanaan,Evaluasi,SuperUser,Pengendalian,Peningkatan,Admin', // adjust roles as needed
        ]);

        $user = User::find($request->user_id);
        $user->role = $request->new_role;
        $user->save();

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user
        ], 200);
    }

    public function getUserHistory()
    {

    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'new_password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::find($request->user_id);
            $user->password = bcrypt($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password'
            ], 500);
        }
    }
}
