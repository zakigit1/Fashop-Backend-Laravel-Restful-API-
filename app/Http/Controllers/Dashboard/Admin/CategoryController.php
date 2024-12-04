<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Traits\imageUploadTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    use imageUploadTrait;

    private const FOLDER_PATH = '/uploads/images/';
    private const FOLDER_NAME = 'categories';
    private const ITEMS_PER_PAGE = 20;

    public function index()
    {
        try {
            $categories = Category::with([
                'translations',
                'children',
                '_parent'
            ])
            ->orderBy('id', 'asc')
            ->paginate(self::ITEMS_PER_PAGE);

            return $this->paginationResponse($categories, 'categories', 'All Categories', SUCCESS_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function show(string $id)
    {
        try {
            $category = $this->findCategoryWithRelations($id);

            $category->load('translations','children','_parent');
            return $this->success($category, 'Category Details', SUCCESS_CODE, 'category');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $category = $this->createCategory($request);
            $this->saveCategoryTranslations($category, $request);

            // $category->load('translations','children','_parent');

            DB::commit();
            return $this->success($category, 'Created Successfully!', SUCCESS_STORE_CODE, 'category');
        } catch (ValidationException $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function update(CategoryRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $category = $this->findCategoryOrFail($id);
            $this->updateCategoryIcon($category, $request);
            $this->updateCategoryDetails($category, $request);
            $this->saveCategoryTranslations($category, $request);

            // $category->load('translations','children','_parent');
            
            DB::commit();
            return $this->success($category, 'Updated Successfully!', SUCCESS_CODE, 'category');
        } catch (ValidationException $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function destroy(string $id)
    {
        try {
            $category = $this->findCategoryOrFail($id);

            if ($this->categoryHasProducts($category) || $this->categoryHasChildren($category)) {
                return $this->error('You Can\'t Delete This Category Because It Has Associated Products or Subcategories!', CONFLICT_ERROR_CODE);
            }

            $this->deleteImage_Trait($category->icon, self::FOLDER_PATH,self::FOLDER_NAME);
            $category->delete();

            return $this->success(null, 'Deleted Successfully!', SUCCESS_DELETE_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    private function findCategoryOrFail(string $id): Category
    {
        $category = Category::find($id);
        
        if (!$category) {
            throw new \Exception('Category Is Not Found!', NOT_FOUND_ERROR_CODE);
        }

        return $category;
    }

    private function findCategoryWithRelations(string $id): Category
    {
        $category = Category::with([
            'translations' => function($query) {
                $query->where('locale', config('translatable.locale'));
            },
            'children' => function($query) {
                $query->with(['translations' => function($query) {
                    $query->where('locale', config('translatable.locale'));
                }]);
            },
            '_parent' => function($query) {
                $query->with(['translations' => function($query) {
                    $query->where('locale', config('translatable.locale'));
                }]);
            }
        ])->find($id);

        if (!$category) {
            throw new \Exception('Category Is Not Found!', NOT_FOUND_ERROR_CODE);
        }

        return $category;
    }

    private function createCategory(CategoryRequest $request): Category
    {
        $iconName = $this->uploadImage_Trait($request, 'icon', self::FOLDER_PATH, self::FOLDER_NAME);

        return Category::create([
            "icon" => $iconName,
            "parent_id" => $this->normalizeParentId($request->parent_id),
            "status" => (int) $request->status,
        ]);
    }

    private function saveCategoryTranslations(Category $category, CategoryRequest $request): void
    {
        foreach (config('translatable.locales.' . config('translatable.locale')) as $keyLang => $lang) {
            $category->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
            $category->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
        }

        $category->save();
    }

    private function updateCategoryIcon(Category $category, CategoryRequest $request): void
    {
        if ($request->hasFile('icon')) {
            $oldIcon = $category->icon;
            $iconName = $this->updateImage_Trait($request, 'icon', self::FOLDER_PATH, self::FOLDER_NAME, $oldIcon);
            $category->update(['icon' => $iconName]);
        }
    }

    private function updateCategoryDetails(Category $category, CategoryRequest $request): void
    {
        $category->update([
            "parent_id" => $this->normalizeParentId($request->parent_id),
            "status" => (int) $request->status,
        ]);
    }

    private function normalizeParentId($parentId)
    {
        return is_null($parentId) ? $parentId : (int) $parentId;
    }

    private function categoryHasProducts(Category $category): bool
    {
        return isset($category->products) && count($category->products) > 0;
    }

    private function categoryHasChildren(Category $category): bool
    {
        return isset($category->children) && count($category->children) > 0;
    }
}
