<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTypeRequest;
use Illuminate\Http\Request;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductTypeController extends Controller
{
    private const ITEMS_PER_PAGE = 20;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $productTypes = ProductType::with('products')
                ->orderBy('id', 'DESC')
                ->paginate(self::ITEMS_PER_PAGE);

            return $this->paginationResponse($productTypes, 'productTypes', 'All Product Types', SUCCESS_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $productType = $this->findProductTypeWithTranslations($id);
            return $this->success($productType, 'Product Type Details', SUCCESS_CODE, 'productType');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            $productType = $this->createProductType($request);
            $this->saveProductTypeTranslations($productType, $request);

            DB::commit();
            return $this->success($productType, 'Created Successfully!', SUCCESS_STORE_CODE, 'productType');
        } catch (ValidationException $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductTypeRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $productType = $this->findProductTypeOrFail($id);
            $this->updateProductTypeDetails($productType, $request);
            $this->saveProductTypeTranslations($productType, $request);

            DB::commit();
            return $this->success($productType, 'Updated Successfully!', SUCCESS_CODE, 'productType');
        } catch (ValidationException $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $productType = $this->findProductTypeOrFail($id);
            $productType->delete();

            return $this->success(null, 'Deleted Successfully!', SUCCESS_DELETE_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    private function findProductTypeOrFail(string $id): ProductType
    {
        $productType = ProductType::find($id);
        
        if (!$productType) {
            throw new \Exception('Product Type Is Not Found!', NOT_FOUND_ERROR_CODE);
        }

        return $productType;
    }

    private function findProductTypeWithTranslations(string $id): ProductType
    {
        $productType = ProductType::with(['translations' => function($query) {
            $query->where('locale', config('translatable.locale'));
        }])->find($id);

        if (!$productType) {
            throw new \Exception('Product Type Is Not Found!', NOT_FOUND_ERROR_CODE);
        }

        return $productType;
    }

    private function createProductType(ProductTypeRequest $request): ProductType
    {
        return ProductType::create([
            "status" => (int) $request->status,
        ]);
    }

    private function saveProductTypeTranslations(ProductType $productType, ProductTypeRequest $request): void
    {
        foreach (config('translatable.locales.' . config('translatable.locale')) as $keyLang => $lang) {
            $productType->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
        }

        $productType->save();
    }

    private function updateProductTypeDetails(ProductType $productType, ProductTypeRequest $request): void
    {
        $productType->update([
            "status" => (int) $request->status,
        ]);
    }
}
