<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Requests\BrandRequest;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    use imageUploadTrait;

    private const FOLDER_PATH = '/uploads/images/';
    private const FOLDER_NAME = 'brands';
    private const ITEMS_PER_PAGE = 20;

    public function index(): JsonResponse
    {
        try {
            $brands = Brand::with('translations')
                ->orderBy('id', 'asc')
                ->paginate(self::ITEMS_PER_PAGE);

            return $this->paginationResponse($brands, 'brands', 'All Brands', SUCCESS_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $brand = $this->findBrandOrFail($id);
            $brand->load('translations');

            return $this->success($brand, 'Brand Details', SUCCESS_CODE, 'brand');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function store(BrandRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $brand = $this->createBrand($request);
            $this->saveBrandTranslations($brand, $request);

            DB::commit();
            return $this->success($brand, 'Created Successfully!', SUCCESS_STORE_CODE, 'brand');
        } catch (ValidationException $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function update(BrandRequest $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $brand = $this->findBrandOrFail($id);
            $this->updateBrandLogo($brand, $request);
            $this->updateBrandStatus($brand, $request);
            $this->saveBrandTranslations($brand, $request);

            DB::commit();
            return $this->success($brand, 'Updated Successfully!', SUCCESS_CODE, 'brand');
        } catch (ValidationException $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $brand = $this->findBrandOrFail($id);
            
            if ($this->brandHasProducts($brand)) {
                return $this->error('You Can\'t Delete This Brand Because They Have Products Communicated With It!', CONFLICT_ERROR_CODE);
            }

            $this->deleteImage_Trait($brand->logo ,self::FOLDER_PATH,self::FOLDER_NAME);
            $brand->delete();

            return $this->success(null, 'Deleted Successfully!', SUCCESS_DELETE_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    private function findBrandOrFail(string $id): Brand
    {
        $brand = Brand::find($id);
        
        if (!$brand) {
            throw new \Exception('Brand Is Not Found!', NOT_FOUND_ERROR_CODE);
        }

        return $brand;
    }

    private function createBrand(BrandRequest $request): Brand
    {
        $logoName = $this->uploadImage_Trait($request, 'logo', self::FOLDER_PATH, self::FOLDER_NAME);

        return Brand::create([
            "logo" => $logoName,
            "status" => (int) $request->status,
        ]);
    }

    private function saveBrandTranslations(Brand $brand, BrandRequest $request): void
    {
        foreach (config('translatable.locales.' . config('translatable.locale')) as $keyLang => $lang) {
            $brand->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
            $brand->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
        }

        $brand->save();
        $brand->load('translations');
    }

    private function updateBrandLogo(Brand $brand, BrandRequest $request): void
    {
        if ($request->hasFile('logo')) {
            $oldLogo = $brand->logo;
            $logoName = $this->updateImage_Trait($request, 'logo', self::FOLDER_PATH, self::FOLDER_NAME, $oldLogo);
            $brand->update(['logo' => $logoName]);
        }
    }

    private function updateBrandStatus(Brand $brand, BrandRequest $request): void
    {
        $brand->update(["status" => (int) $request->status]);
    }

    private function brandHasProducts(Brand $brand): bool
    {
        return isset($brand->products) && count($brand->products) > 0;
    }
}
