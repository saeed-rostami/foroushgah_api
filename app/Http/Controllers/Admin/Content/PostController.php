<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\Content\PostResource;
use App\Http\Services\Image\ImageService;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Cache::remember('posts', 3600, function () {
            return PostResource::collection(Post::all());
        });
        return response()->json([
            'posts' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

            $image = $this->prepareImage($request, null, $imageService);

//            PREPARE AND STORE TAGS
            $tags = $this->prepareTags($request);

            $categoryID = $this->getCategory($request);

//            STORE FINALLY
            $post = new Post();
            $post->title = $request->title;
            $post->category_id = $categoryID;
            $post->slug = Str::slug($request->title);
            $post->body = $request->body;
            $post->summary = $request->summary;
            $post->status = $request->status;
            $post->commentable = $request->commentable;
            $post->tags = $tags;
            $post->published_at = $request->published_at;
//            $post->author_id = 3;
//            TODO USERS TABLES SHOULD NOT BE EMPTY
            $post->image = $image;
            $post->save();

            //RESPONSE
            return response()->json([
                'message' => '???? ???????????? ?????????? ????',
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
    public function update(Request $request, $id, ImageService $imageService)
    {
        try {
            //            VALIDATION REQUEST
            $this->validation($request, 'update');
            $post = Post::query()->find($id);

            //            STORE IMAGE FILE
            $image = $this->prepareImage($request, $post, $imageService);

            //            PREPARE AND STORE TAGS
            $tags = $this->prepareTags($request);

//            GET CATEGORY_ID
            $categoryID = $this->getCategory($request);

            //            STORE FINALLY
            $post->update([
                'title' => $request->title,
                'category_id' => $categoryID,
                'slug' => Str::slug($request->title),
                'body' => $request->body,
                'summary' => $request->summary,
                'status' => $request->status,
                'commentable' => $request->commentable,
                'published_at' => $request->published_at,
                'tags' => $tags,
                'image' => $image
            ]);


            //RESPONSE
            return response()->json([
                'message' => '???? ???????????? ???????? ????',
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
        $post = Post::query()->find($id);
        $post->delete();
        return response()->json([
            'message' => '???? ???????????? ?????? ????',
            'status' => 204
        ]);
    }

    protected function validation(Request $request, $method)
    {
        if ($method == 'store') {
            $this->validate($request, [
                'title' => 'required|string|max:32|min:2',
                'category_id' => 'required',
                'body' => 'required|string|min:5',
                'summary' => 'required|string|min:5',
                'slug' => 'string|unique:posts,slug',
                'image' => 'image:mimes:jpg,png,jpeg|max:2048',
                'status' => 'required',
                'commentable' => 'required',
                'tags' => 'string'
            ]);
        } else {
            $this->validate($request, [
                'title' => 'required|string|max:32|min:2',
                'category_id' => 'required',
                'body' => 'required|string|min:5',
                'slug' => 'string|unique:posts,slug',
                'summary' => 'required|string|min:5',
                'status' => 'required',
                'commentable' => 'required',
                'tags' => 'string'
            ]);
        }

    }

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
     * @return int|mixed|string
     */
    protected function getCategory(Request $request)
    {
        if (is_numeric($request->category_id)) {
            $categoryID = $request->category_id;
        } else {
            $category = PostCategory::query()
                ->where('name', $request->category_id)
                ->first();
            $categoryID = $category->id;
        }
        return $categoryID;
    }

    /**
     * @param Request $request
     * @param $post
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
                . 'post');
            $image = $imageService->createIndexAndSave($requestImage);
            return $image;
        }
    }
}
