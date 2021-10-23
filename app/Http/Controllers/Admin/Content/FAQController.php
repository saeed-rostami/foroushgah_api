<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\Content\FAQResource;
use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faqs = FAQResource::collection(FAQ::all());
        return response()->json([
            'faqs' => $faqs
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

            $faq = new FAQ();
            $faq->question = $request->question;
            $faq->answer = $request->answer;
            $faq->slug = Str::slug($request->question);
            $faq->status = $request->status;
            $faq->tags = $tags;
            $faq->save();

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
     * @param  \App\FAQ $fAQ
     * @return \Illuminate\Http\Response
     */
    public function show(FAQ $fAQ)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\FAQ $fAQ
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $this->validation($request);


            $tags = $this->prepareTags($request);
            $faq = FAQ::query()->find($id);

            $faq->update([
                'question' => $request->question,
                'answer' => $request->answer,
                'slug' => Str::slug($request->question),
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
     * @param  \App\FAQ $fAQ
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = FAQ::query()->find($id);
        $faq->delete();

        return response()->json([
            'message' => 'با موفقیت حذف شد',
            'status' => 204
        ]);
    }

    protected function validation(Request $request)
    {

        $this->validate($request, [
            'question' => 'required|string|min:2',
            'answer' => 'required|string',
            'slug' => 'string|unique:faqs,slug',
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
