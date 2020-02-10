<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Item;
use App\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\ItemResource
     */
    public function store(Request $request, $menu)
    {
        $data = $request->validate([
            '*.field' => ['required', 'string'],
            '*.parent' => [
                'nullable',
                'numeric',
                Rule::exists(Item::class, 'id')->where(static function ($query) use ($menu) {
                    $query->where('menu_id', $menu);
                })
            ]
        ]);

        /** @var Menu $menu */
        $menu = Menu::findOrFail($menu);

        $menu->guardForMaxDepth();

        $menu->guardForMaxChildren(count($data));

        $tree = $menu->createChildren($data);

        return ItemResource::make($tree, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $menu
     * @return \App\Http\Resources\ItemResource
     */
    public function show($menu)
    {
        return Menu::buildTree($menu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($menu)
    {
        Menu::deleteItems($menu);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
