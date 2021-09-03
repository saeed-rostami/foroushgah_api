<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\Content\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = PageResource::collection(Page::all());
        return response()->json([
            'pages' => $pages
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
            $this->validation($request);

            $tags = $this->prepareTags($request);

            $page = new Page();
            $page->title =$request->title;
            $page->body =$request->body;
            $page->slug = Str::slug($request->title);
            $page->status = $request->status;
            $page->tags = $tags;
            $page->save();

            return response()->json([
                'message' => 'با موفقیت ایجاد شد',
                'status' => 201
            ] );

        } catch (ValidationException $error) {
            return response($error->errors());

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validation($request);


            $tags = $this->prepareTags($request);
            $page = Page::query()->find($id);

            $page->update([
                'title' => $request->title,
                'body' => $request->body,
                'slug' => Str::slug($request->title),
                'status' => $request->status,
                'tags' => $tags,
            ]);

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
     * @param  \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $page = Page::query()->find($id);
        $page->delete();
        return response()->json([
            'message' => 'با موفقیت حذف شد',
            'status' => 204
        ]);

    }


    protected function validation(Request $request)
    {

        $this->validate($request, [
            'title' => 'required|string|min:2',
            'body' => 'required|string',
            'slug' => 'string|unique:faqs',
            'status' => 'required',
            'tags' => 'string'
        ]);
    }

    protected function prepareTags(Request $request)
    {
        $arrayTags = explode(',', $request->tags);
        foreach ($arrayTags as $tag) {
            $tags[] = $tag;
        }
        return $tags;
    }
}
