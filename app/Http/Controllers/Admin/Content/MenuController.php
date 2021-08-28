<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = MenuResource::collection(Menu::all());
        return response()->json([
            'menus' => $menus,
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

            $menu = new Menu();
            $menu->name = $request->name;
            $menu->url = $request->url;
            $menu->status = $request->status;
            $menu->parent_id = $request->parent_id;
            $menu->save();

            return response()->json([
                'message' => 'با موفقیت ایجاد شد',
                'status' => 200
            ]);

        } catch (ValidationException $error) {
            throw $error->errors();
        }
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
            $this->validation($request);

            $menu = Menu::query()->find($id);
            $menu->update([
                'name' => $request->name,
                'url' => $request->url,
                'status' => $request->status,
                'parent_id' => $request->parent_id,
            ]);

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
        $menu = Menu::query()->find($id);
        $menu->delete();
        return response()->json([
            'message' => 'با موفقیت حذف شد',
            'status' => 200
        ]);
    }

    protected function validation(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|:max:32|min:2',
            'url' => 'required|string',
            'status' => 'required',
            'parent_id' => 'nullable'
        ]);


    }
}
