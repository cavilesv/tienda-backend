<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Validation\ValidationException;
use Barryvdh\Debugbar\Facades\Debugbar;

define('URL_HOST', env('APP_URL')); 

class ProductsController extends Controller
{
    
    /* AGREGA NUEVO PRODUCTO ----------------------------------------------------------------------*/

    public function addProduct(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
                return response()->json(['error' => "Campo '$key' no tiene codificación UTF-8 válida"], 400);
            }
        }

        define('CATEGORIA_POR_DEFECTO', "Sin categoría"); 


        try {
            $request->validate([
                'imagen' => 'required|image|mimes:jpg,jpeg,png,gif|max:20480', 
                'nombre' => 'required|string|unique:products,name|min:3|max:40',
                'precio' => 'required|max:8',
                'categoria' => 'nullable|string|max:40',
                'descripcion' => 'nullable|string|max:200'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error de validación.',
                'errores' => $e->errors()
            ], 422);
        }

        $imagen = $request->file("imagen");
        $mime = $imagen->getMimeType();
        /* $imagen_procesada = file_get_contents(mb_convert_encoding($imagen->getRealPath(), 'UTF-8', 'auto')); */
        $imagen_procesada = file_get_contents($imagen->getRealPath());

        $product = new Product();
        $product->name = $request->nombre;
        $product->price = $request->precio;
        $product->category_id = ($request->categoria == CATEGORIA_POR_DEFECTO) ? 0 : Category::where('name', $request->categoria)->first()->id;
        $product->description = utf8_decode($request->descripcion) ?? '';  // Asegúrate de que esté bien codificada

        $product->image = $imagen_procesada;
        $product->mime = $mime;
        
        $product->save();

        // Eliminar los campos no necesarios antes de devolver la respuesta
        // Respuesta limpia
        return response()->json([
            'success' => true,
            'message' => 'Petición procesada correctamente.'
        ]);  // Respuesta 204
    }

    /* FIN AGREGAR NUEVO PRODUCTO ----------------------------------------------------------------------------------------------- */


    public function updateProduct(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:products,id',
                'name' => 'required|string',
                'price' => 'required|numeric',
                'category' => 'required|string',
                'description' => 'nullable|string',
            ]);

            $product = Product::find($request->id);
            $category = Category::where("name", $request->category)->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'errores' => ['category' => ['Categoría no encontrada']],
                ], 404);
            }

            $product->name = $request->name;
            $product->price = $request->price;
            $product->category_id = $category->id;
            $product->description = $request->description;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errores' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado',
                'errores' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }

    public function removeProduct(Request $request)
    {
        Product::destroy($request->id);
        return;
    }

    public function getProduct(Request $request)
    {
        return Product::find($request->id);
    }

   /*  public function getListProducts(Request $request)
    {
        try {
            $query = Product::query();
            $filtro = $request->input('filtro');
            $termino = $request->input('termino');

            if (!empty($termino)) {
                if ($filtro === 'nombre') {
                    $query->where('name', 'LIKE', '%' . $termino . '%');
                } elseif ($filtro === 'categoria') {
                    $ids_categories = Category::where('name', 'LIKE', '%' . $termino . '%')
                        ->pluck('id')
                        ->toArray();
                    $query->whereIn('category_id', $ids_categories);
                }
            }

            $products = $query->paginate(20, ['*'], 'page', $request->input('page'));
            return response()->json($this->transformCollection($products));
        } catch (\Throwable $e) {
            \Log::error('Error en getListProducts: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    } */

    public function getListProducts(Request $request)
    {
        $query = Product::with('category');
        $filtro = $request->input('filtro');
        // Filtros opcionales
        $termino = $request->input('termino') ?? null;
        if (!empty($termino)) {
            switch ($filtro)
            {
                case 'nombre' : $query->where('name', 'LIKE', '%' . $termino . '%');
                                $products = $query->paginate(20, ['*'], 'page', $request->input('page'));
                                return response()->json($this->transformCollection($products));
                                break;  
                               
                case 'categoria' : 
                    $ids_categories = Category::where('name', 'LIKE', '%'. $termino . '%')->get()->pluck('id')->toArray();
                    $query->whereIn('category_id', $ids_categories);
                    $products = $query->paginate(20, ['*'], 'page', $request->input('page'));
                    return response()->json($this->transformCollection($products));
                    break;                  
         
            }            
        }
        
        $products = $query->paginate(20, ['*'], 'page', $request->input('page'));       
        return response()->json($this->transformCollection($products));
    } 

    private function transformCollection($products)
    {
        $products->getCollection()->transform(function ($product) {
            // Usar el id del producto para generar la URL
            $product->image =  "http://localhost/productos/imagen/" . $product->id;
            
            $category = $product->category->name;
            unset($product->category);
            $product->category = $category ?? 'Sin categoría';
            
            return $product;
            

        // Si es necesario convertir el blob a una cadena base64 para la imagen
        // $product->image_data = base64_encode($product->image); // Comentado si no se quiere codificar en base64            
        });
        return $products;
    }

    public function getImage($id)
    {
        // Buscar el producto por su ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

         // Verifica si el producto tiene una imagen (en formato blob)
         if (!$product->image) {
            return response()->json(['error' => 'Imagen no disponible'], 404);
        }

        // Obtener el MIME type de la imagen desde el campo 'mime'
        $mimeType = $product->mime;

        // Verificar que el MIME type no sea nulo o vacío
        if (empty($mimeType)) {
            return response()->json(['error' => 'Tipo de imagen no especificado'], 400);
        }

        // Retornar la imagen en formato blob con el MIME type correcto
        return response($product->image)
            ->header('Content-Type', $mimeType);  // Especificar el tipo MIME
    }
}
