<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductGalleryRequest;
use App\Models\Product;
use App\Models\ProductImageGallery;
use App\Traits\imageUploadTrait;
use Illuminate\Http\Request;

class ProductGalleryController extends Controller
{
    use imageUploadTrait;

    private const FOLDER_PATH = '/uploads/images/products/';
    private const FOLDER_NAME = 'gallery';
    private const ITEMS_PER_PAGE = 20;

    public function index()
    {
        try {
            $productGallery = ProductImageGallery::orderBy('id', 'ASC')
                ->paginate(self::ITEMS_PER_PAGE);

            return $this->paginationResponse($productGallery, 'productGallery', 'Product Gallery', SUCCESS_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function store(ProductGalleryRequest $request , int $productId)
    {
        // return $productId;
        try {
            $product = $this->findProductOrFail($productId);
            $imagesNames = $this->upload_Multi_Image_Trait($request, 'image', self::FOLDER_PATH, self::FOLDER_NAME);
            
            $productGallery = $this->createGalleryImages($imagesNames, $productId);

            return $this->success(
                $productGallery, 
                'Product images added successfully', 
                SUCCESS_STORE_CODE, 
                'productGallery'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }

    public function destroy(string $id)
    {
        try {
            $productGallery = $this->findGalleryImageOrFail($id);
            $this->deleteGalleryImage($productGallery);

            return $this->success(null, 'Image Has Been Deleted Successfully!', SUCCESS_DELETE_CODE);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }

    public function destroyAllImages(string $id)
    {
        try {
            $product = $this->findProductOrFail($id);
            $this->deleteAllProductImages($product);

            return $this->success(null, 'Product Gallery Has Been Deleted Successfully!', SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }

    private function findProductOrFail(int $productId): Product
    {
        $product = Product::find($productId);
        
        if (!$product) {
            throw new \Exception('Product not found', NOT_FOUND_ERROR_CODE);
        }

        return $product;
    }

    private function findGalleryImageOrFail(string $id): ProductImageGallery
    {
        $productGallery = ProductImageGallery::find($id);
        
        if (!$productGallery) {
            throw new \Exception('Product Image Is Not Found!', NOT_FOUND_ERROR_CODE);
        }

        return $productGallery;
    }

    private function createGalleryImages(array $imagesNames, int $productId): array
    {
        return collect($imagesNames)->map(function ($imageName) use ($productId) {
            return ProductImageGallery::create([
                'product_id' => $productId,
                'image' => $imageName,
            ]);
        })->toArray();
    }

    private function deleteGalleryImage(ProductImageGallery $productGallery): void
    {
        $this->deleteImage_Trait($productGallery->image, self::FOLDER_PATH, self::FOLDER_NAME);
        $productGallery->delete();
    }

    private function deleteAllProductImages(Product $product): void
    {
        if (isset($product->gallery) && count($product->gallery) > 0) {
            foreach ($product->gallery as $productImage) {
                $this->deleteGalleryImage($productImage);
            }
        }
    }
}
