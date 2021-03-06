<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\Content\PostCategoriesResource;
use App\Http\Services\Image\ImageService;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $postCategories = Cache::remember('postCategories', 3600, function () {
            return PostCategoriesResource::collection(PostCategory::all());
        });


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
    public function store(Request $request, ImageService $imageService)
    {

        try {
//            VALIDATION REQUEST
            $this->validation($request, 'store');

//            STORE IMAGE FILE
            $image = $this->prepareImage($request, null, $imageService);

//            PREPARE AND STORE TAGS
            $tags = $this->prepareTags($request);

//            STORE FINALLY
            $postCategory = new PostCategory();
            $postCategory->name = $request->name;
            $postCategory->slug = Str::slug($request->name);
            $postCategory->description = $request->description;
            $postCategory->status = $request->status;
            $postCategory->tags = $tags;
            $postCategory->image = $image;
            $postCategory->save();


            //RESPONSE
            return response()->json([
                'message' => 'با موفقیت ایجاد شد',
                'status' => 201
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
    public function update(Request $request, $id , ImageService $imageService)
    {
        try {
            //            VALIDATION REQUEST
            $this->validation($request, 'update');
            $postCategory = PostCategory::query()->find($id);

            //            STORE IMAGE FILE
            $image_name = $this->prepareImage($request, $postCategory , $imageService);

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
                'status' => 204
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
            'status' => 204
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
                'name' => 'required|string|max:32|min:2',
                'description' => 'required|string|min:5',
                'slug' => 'string|unique:post_categories,slug',
                'image' => 'image:mimes:jpg,png,jpeg|max:2048',
                'status' => 'required',
                'tags' => 'string'
            ]);
        } else {
            $this->validate($request, [
                'name' => 'required|string|max:32|min:2',
                'description' => 'required|string|min:5',
                'slug' => 'string|unique:post_categories,slug',
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
     * @param null $post
     * @param $imageService
     * @return string
     */
    protected function prepareImage(Request $request, $post = null, $imageService)
    {
        if ($request->file('image')) {
            if (!empty($post->image)) {
                $imageService->deleteDirectoryAndFiles($post->image['directory']);
            }
            $requestImage = $request->file('image');
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR
                . 'category');
            $image = $imageService->createIndexAndSave($requestImage);
            return $image;
        }
    }
}
