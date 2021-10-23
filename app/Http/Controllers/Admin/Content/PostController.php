<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\Content\PostResource;
use App\Models\Post;
use App\Models\PostCategory;
use App\Services\ImageIntervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
        $posts = PostResource::collection(Post::all());
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
    public function store(Request $request)
    {
        try {
//            VALIDATION REQUEST
            $this->validation($request, 'store');

//            STORE IMAGE FILE
            $image_name = $request->name . $request->image->getClientOriginalName();
            $request->file('image')->storeAs('images/content/post', $image_name, 'public');

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
            $post->image = $image_name;
            $post->save();

            $path = "storage/images/content/post/";
            ImageIntervention::Resize($path, $image_name, '525', '295');

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
    public function update(Request $request, $id)
    {
        try {
            //            VALIDATION REQUEST
            $this->validation($request, 'update');
            $post = Post::query()->find($id);

            //            STORE IMAGE FILE
            $image_name = $this->prepareImage($request, $post);

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
                'image' => $image_name
            ]);

            $path = "storage/images/content/post/";
            ImageIntervention::Resize($path, $image_name, '525', '295');


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
        $post = Post::query()->find($id);
        $post->delete();
        return response()->json([
            'message' => 'با موفقیت حذف شد',
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
     * @return string
     */
    protected function prepareImage(Request $request, $post)
    {
        if ($request->hasFile('image')) {
            File::delete("storage/images/content/post/" . $post->image);
            $image_name = $request->title . $request->image->getClientOriginalName();

            $request->file('image')->storeAs('images/content/post', $image_name, 'public');
        } else {
            $image_name = $post->image;
        }
        return $image_name;
    }
}
