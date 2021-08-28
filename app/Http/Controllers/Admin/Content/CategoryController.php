<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCategoriesResource;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $postCategories = PostCategoriesResource::collection(PostCategory::all());
        return response()->json([
            'categories' => $postCategories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
//            VALIDATION REQUEST
            $this->validation($request, 'store');

//            STORE IMAGE FILE
            $image_name = $request->name . $request->image->getClientOriginalName();
            $request->file('image')->storeAs('images/content/category', $image_name, 'public');

//            PREPARE AND STORE TAGS
            $tags = $this->prepareTags($request);

//            STORE FINALLY
            $postCategory = new PostCategory();
            $postCategory->name = $request->name;
            $postCategory->slug = Str::slug($request->name);
            $postCategory->description = $request->description;
            $postCategory->status = $request->status;
            $postCategory->tags = $tags;
            $postCategory->image = $image_name;
            $postCategory->save();

            $img = Image::make('storage/images/content/category/' . $image_name)->resize('525', '295');
            $img->save();

            //RESPONSE
            return response()->json([
                'message' => 'با موفقیت ایجاد شد',
                'status' => 200
            ]);
        } catch (ValidationException $error) {
            return response($error->errors());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            //            VALIDATION REQUEST
            $this->validation($request, 'update');
            $postCategory = PostCategory::query()->find($id);

            //            STORE IMAGE FILE
            $image_name = $this->prepareImage($request, $postCategory);

            //            PREPARE AND STORE TAGS
            $tags = $this->prepareTags($request);

            //            STORE FINALLY
            $postCategory->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'status' => $request->status,
                'tags' => $tags,
                'image' => $image_name
            ]);


            //RESPONSE
            return response()->json([
                'message' => 'با موفقیت بروز شد',
                'status' => 200
            ]);
        } catch (ValidationException $error) {
            return response($error->errors());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $postCategory = PostCategory::query()->find($id);
        $postCategory->delete();
        return response()->json([
            'message' => 'با موفقیت حذف شد',
            'status' => 200
        ]);

    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    protected function validation(Request $request, $method)
    {
        if ($method == 'store') {
            $this->validate($request, [
                'name' => 'required|string|:max:32|min:2',
                'description' => 'required|string|min:5',
                'image' => 'image:mimes:jpg,png,jpeg|max:2048',
                'status' => 'required',
                'tags' => 'string'
            ]);
        } else {
            $this->validate($request, [
                'name' => 'required|string|:max:32|min:2',
                'description' => 'required|string|min:5',
                'status' => 'required',
                'tags' => 'string'
            ]);
        }

    }

    /**
     * @param Request $request
     * @param array $tags
     * @return array
     */
    protected function prepareTags(Request $request)
    {
        $arrayTags = explode(',', $request->tags);
        foreach ($arrayTags as $tag) {
            $tags[] = $tag;
        }
        return $tags;
    }

    /**
     * @param Request $request
     * @param $postCategory
     * @return string
     */
    protected function prepareImage(Request $request, $postCategory)
    {
        if ($request->hasFile('image')) {
            File::delete("storage/images/content/category/" . $postCategory->image);
            $image_name = $request->name . $request->image->getClientOriginalName();
            $request->file('image')->storeAs('images/content/category', $image_name, 'public');
            $img = Image::make('storage/images/content/category/' . $image_name)->resize('525', '295');
            $img->save();
        } else {
            $image_name = $postCategory->image;
        }
        return $image_name;
    }
}
