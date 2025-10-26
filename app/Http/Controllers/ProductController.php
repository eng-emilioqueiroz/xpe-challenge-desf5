<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $service) {}

    public function store(ProductStoreRequest $req): JsonResponse {
        $product = $this->service->create($req->validated());
        return response()->json($product, 201);
    }

    public function index(Request $req): JsonResponse {
        $perPage = $req->integer('per_page');
        return response()->json($this->service->findAll($perPage));
    }

    public function show(int $id): JsonResponse {
        $p = $this->service->findById($id);
        return $p ? response()->json($p) : response()->json(['message'=>'Not Found'], 404);
    }

    public function searchByName(Request $req): JsonResponse {
        $name = (string) $req->query('name', '');
        return response()->json($this->service->findByName($name));
    }

    public function update(ProductUpdateRequest $req, int $id): JsonResponse {
        $p = $this->service->findById($id);
        if (!$p) return response()->json(['message'=>'Not Found'], 404);
        return response()->json($this->service->update($p, $req->validated()));
    }

    public function destroy(int $id): JsonResponse {
        $p = $this->service->findById($id);
        if (!$p) return response()->json(['message'=>'Not Found'], 404);
        $this->service->delete($p);
        return response()->json([], 204);
    }

    public function count(): JsonResponse {
        return response()->json(['count' => $this->service->count()]);
    }
}