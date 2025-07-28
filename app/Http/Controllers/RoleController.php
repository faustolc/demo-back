<?php

namespace App\Http\Controllers;

use App\Exports\RolesExport;
use App\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'authorized_sections' => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], Response::HTTP_BAD_REQUEST);
        }
        $role = Role::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role,
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);
        if (! $role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $role,
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);
        if (! $role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'authorized_sections' => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], Response::HTTP_BAD_REQUEST);
        }
        // Update the role with the validated data
        $role->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $role,
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        if (! $role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }
        $role->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Export roles to Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new RolesExport(), 'roles.xlsx');
    }
    /**
     * Export roles to PDF.
     */
    public function exportPdf()
    {
        $roles = Role::all();
        $pdf = Pdf::loadView('roles.pdf', compact('roles'));

        return $pdf->download('roles.pdf');
    }
}
