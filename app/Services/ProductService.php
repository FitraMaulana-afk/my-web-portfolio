<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Throwable;
use App\Services\BaseService;

class ProductService extends BaseService
{
    private $product;
    private ?string $gambarBaru = null;
    private ?string $gambarLama = null;

    public function __construct()
    {
        $this->product = new Product();
    }

    public function index(Request $request):object
    {
        $product = $this->product->query();
        return $this->response(true,'selamat anda berhasil',$product);
    }

    public function store(ProductRequest $request) : Product
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            $this->gambarBaru = $request->file('gambar')->store('img', 'public');
            $data['gambar'] = $this->gambarBaru;
        }
        $product = $this->product->create($data);

        return $product;
    }

    public function update(ProductRequest $request,Product $product): Product
    {
        $data = $request->validated();
        if ($request->hasFile('gambar')) {
            $this->gambarBaru = $request->file('gambar')->store('img', 'public');
            $data['gambar'] = $this->gambarBaru;
        }
        $gambarLama = $product->gambar;
        $product->update($data);
        Storage::disk('public')->delete($gambarLama);

        return $product;
    }

    public function destroy(Product $product)
    {
        $gambarLama = $product->gambar;
        $product->delete();
        if(! empty($gambarLama) && (Storage::disk('public'))->exists($gambarLama))
        {
            Storage::disk('public')->delete($gambarLama);
        }
        return $product;
    }
}