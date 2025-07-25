<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): JsonResponse
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'data' => $users,
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['nullable', 'email', 'max:255', 'unique:users'],
                'picture_profile' => ['nullable', 'string'],
                'phone' => ['nullable', 'string', 'max:20'],
                'password' => ['required', Rules\Password::defaults()],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'error' => $e->getMessage(),
            ], 422);
        }

        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'picture_profile' => $request->picture_profile,
            'phone' => $request->phone,
            'roles' => ['user'], // Default role
        ];

        $user = User::create($userData);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        try {
            $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'username' => ['sometimes', 'string', 'max:255', 'unique:users,username,' . $id],
                'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $id],
                'picture_profile' => ['nullable', 'string'],
                'phone' => ['nullable', 'string', 'max:20'],
                'password' => ['sometimes', Rules\Password::defaults()],
                'roles' => ['nullable', 'array'],
            ]);

            $updateData = $request->only([
                'name',
                'username',
                'email',
                'picture_profile',
                'phone',
                'roles',
            ]);

            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Test MongoDB connection.
     */
    public function testConnection(): JsonResponse
    {
        try {
            $count = User::count();

            return response()->json([
                'success' => true,
                'message' => 'MongoDB connection successful',
                'users_count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'MongoDB connection failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export users to Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new UsersExport(), 'products.xlsx');
    }

    /**
     * Export users to PDF.
     */
    public function exportPdf()
    {
        $users = User::all();
        $pdf = Pdf::loadView('users.pdf', compact('users'));

        return $pdf->download('users.pdf');
    }

    /**
     * Upload user profile photo.
     */
    public function uploadPhoto(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Validación del archivo
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'], // 2MB Max
        ]);

        // Si el usuario ya tiene una foto, la eliminamos de S3 para no guardar basura
        if ($user->picture_profile) {
            Storage::disk('s3')->delete($user->picture_profile);
        }

        // Guardamos la nueva foto en S3 y obtenemos la ruta
        $path = $request->file('photo')->store('profile-photos', 's3');

        // Actualizamos el documento del usuario en MongoDB con la nueva ruta
        $user->forceFill([
            'picture_profile' => $path,
        ])->save();

        // Retornamos el usuario actualizado con la nueva URL de la foto
        return response()->json($user->fresh());
    }
}
