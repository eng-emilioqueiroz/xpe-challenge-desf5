<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function create(array $data): Product { return Product::create($data); }

    public function findAll(?int $perPage = null): Collection|LengthAwarePaginator {
        $query = Product::query()->orderByDesc('created_at');
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function findById(int $id): ?Product { return Product::find($id); }

    public function findByName(string $name): Collection {
        return Product::where('name', 'LIKE', "%{$name}%")->orderBy('name')->get();
    }

    public function update(Product $product, array $data): Product {
        $product->fill($data)->save();
        return $product;
    }

    public function delete(Product $product): void { $product->delete(); }

    public function count(): int { return Product::count(); }
}
