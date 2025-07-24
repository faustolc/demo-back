<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Ensure the Product model is imported
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        return response()->json($products);
    }

    /**
     * Show the details of a specific resource.
     */
    public function show(string $id)
    {
        // 1. Buscar el producto por ID
        $product = Product::find($id);

        // 2. Si no se encuentra, retornar un error 404
        if (! $product) {
            return response()->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        // 3. Retornar el producto encontrado
        return response()->json($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:products,code|max:255',
            'name' => 'required|string',
            'brand' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], Response::HTTP_BAD_REQUEST);
        }

        // 2. Crear el nuevo producto
        $product = Product::create($request->all());

        // 3. Retornar una respuesta JSON con el producto creado y un código de estado 201 (Created)
        return response()->json($product, Response::HTTP_CREATED);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // 1. Buscar el producto por ID
        $product = Product::find($id);

        // El producto ya está cargado gracias al Route Model Binding
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. Buscar el producto por ID
        $product = Product::find($id);

        // 1. Validar los datos de entrada
        $request->validate([
            'code' => 'required|string|unique:products,code,' . $product->id . '|max:255', // El código debe ser único, excepto para este producto
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        // 2. Actualizar el producto
        $product->update($request->all());

        // 3. Retornar una respuesta JSON con el producto actualizado
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 1. Buscar el producto por ID
        $product = Product::find($id);

        // 1. Eliminar el producto
        $product->delete();

        // 2. Retornar una respuesta vacía con un código de estado 204 (No Content)
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function exportExcel()
    {
        return Excel::download(new ProductsExport(), 'products.xlsx');
    }

    public function exportPdf()
    {
        $products = Product::all();
        $pdf = Pdf::loadView('products.pdf', compact('products'));

        return $pdf->download('products.pdf');
    }
}
