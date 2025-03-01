<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\CategoryDescription;
use App\Traits\CustomFileTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    use CustomFileTrait;
    protected $path = '';

    public function __construct()
    {
        $this->path = public_path(config('constant.file_path.category'));
    }

    public function index(Request $request)
    {
        $name = $request->get('name', '');
        $records = Category::select('category_id', 'image', 'parent_id', 'sort_order', 'status', 'men', 'women')
            ->with('admincategoryDescription')
            ->when($name != '', function ($q) use ($name) {
                $q->whereHas('admincategoryDescription', function ($q) use ($name) {
                    $q->where('name', 'like', "%$name%");
                });
            })->whereDeletedAt(null)->orderBy('created_at', 'DESC')->paginate($this->defaultPaginate);

        return view('admin.category.index', ['records' => $records]);
    }

    public function add()
    {
        $parentCategory = CategoryDescription::parentCategory();
        return view('admin.category.add', ['parentCategory' => $parentCategory]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'multilanguage.*.name' => ['required', 'string', 'max:255'],
            'image' => ['required', 'mimes:jpeg,jpg,png']
        ], [
            'multilanguage.*.name.required' => 'Name is required'
        ]);

        $this->createDirectory($this->path);
        $category = new Category($request->only('sort_order', 'status', 'parent_id'));
        $category->image = $this->saveCustomFileAndGetImageName(request()->file('image'), $this->path);
        $category->image = "uploads/category/$category->image";

        if ($request->gender == 'men')
            $category->men = 1;
        if ($request->gender == 'women')
            $category->women = 1;
        if ($request->gender == 'both')
            $category->men = $category->women = 1;

        $category->save();

        $categoryDescription = new CategoryDescription();
        $buildMultiLanguage = $categoryDescription->buildMultiLang($category->category_id, $request->multilanguage);
        $categoryDescription->upsert($buildMultiLanguage, ['category_id', 'language_id', 'name', 'meta_keyword', 'meta_title', 'meta_description']);

        return redirect(route('category'))->with('success', 'Category Created Successfully');
    }

    public function edit($id)
    {


        return view('admin.category.edit', [
            'data' => Category::with('categoryMultipleDescription')->findOrFail($id),
            'parentCategory' => CategoryDescription::parentCategory()
        ]);
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'multilanguage.*.name' => ['required', 'string', 'max:255'],
        ], [
            'multilanguage.*.name.required' => 'Name is required'
        ]);

        //Update Category
        $category = Category::findOrFail($id);
        //        $category = new Category($request->only('sort_order','status','parent_id'));
        if ($request->hasFile('image')) {
            $currentCategoryImage = $this->path . '/' . $category->image;
            if (File::exists($currentCategoryImage)) {
                unlink($currentCategoryImage);
            }
            $category->image = $this->saveCustomFileAndGetImageName(request()->file('image'), $this->path);
            $category->image = "uploads/category/$category->image";
        }

        if ($request->gender == 'men')
            $category->men = 1;
        if ($request->gender == 'women')
            $category->women = 1;
        if ($request->gender == 'both')
            $category->men = $category->women = 1;

        $category->fill($request->only('sort_order', 'status', 'parent_id'))->save();

        //Update Category Description
        $categoryDescription = new CategoryDescription();
        $categoryDescription->where('category_id', $id)->delete();
        $buildMultiLanguage = $categoryDescription->buildMultiLang($id, $request->multilanguage);
        $categoryDescription->upsert($buildMultiLanguage, ['category_id', 'language_id', 'name', 'meta_keyword', 'meta_title', 'meta_description']);

        return redirect(route('category'))->with('success', 'Category Updated Successfully');
    }

    public function delete($id)
    {
        if (!$category = Category::whereCategoryId($id)->first()) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
        $categoryIds = [];
        if ($category->parent_id == 0) {
            $categoryIds = Category::where('parent_id', $id)->pluck('category_id')->toArray();
        }
        array_push($categoryIds, $category->category_id);

        $images = Category::whereIn('category_id', $categoryIds)->pluck('image');

        foreach ($images as $key => $value) {
            $this->removeOldImage($value, $this->path);
        }

        CategoryDescription::whereIn('category_id', $categoryIds)->delete();
        Category::whereIn('category_id', $categoryIds)->delete();

        return redirect(route('category'))->with('success', 'Category  Deleted Successfully');
    }

    public function getParentCategoryGender($id)
    {
        $category = Category::find($id);

        if ($category) {
            if ($category->men == 1 && $category->women == 1)
                return response()->json(['parent_gender' => 'both']);
            elseif ($category->men == 1 && $category->women == 0)
                return response()->json(['parent_gender' => 'men']);
            elseif ($category->men == 0 && $category->women == 1)
                return response()->json(['parent_gender' => 'women']);
            elseif ($category->men == 0 && $category->women == 0)
                return response()->json(['parent_gender' => 'none']);
        }

        return response()->json(['error' => 'Category not found'], 404);
    }
}
