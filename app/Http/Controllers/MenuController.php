<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuResource;
use App\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\MenuResource
     */
    public function store(Request $request)
    {
        $request->validate([
            'field' => ['required', 'string'],
            'max_depth' => ['sometimes', 'required', 'numeric', 'min:1'],
            'max_children' => ['sometimes', 'required', 'numeric', 'min:1']
        ]);

        return MenuResource::make(
            Menu::create($request->all())
        );
    }

    /**
     * Display the specified resource.
     *
     * @param integer $menu
     * @return \App\Http\Resources\MenuResource
     */
    public function show($menu)
    {
        return MenuResource::make(
            Menu::findOrFail($menu)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $menu
     * @return \App\Http\Resources\MenuResource
     */
    public function update(Request $request, $menu)
    {
        $data = $request->validate([
            'field' => ['required', 'string'],
            'max_depth' => ['sometimes', 'required', 'numeric', 'min:1'],
            'max_children' => ['sometimes', 'required', 'numeric', 'min:1']
        ]);

        $menu = Menu::findOrFail($menu);

        $menu->fill($data)->save();

        return MenuResource::make($menu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $menu
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Menu $menu)
    {
        // TODO: remove items.
        $menu->delete();

        return response()->json(
            [], Response::HTTP_NO_CONTENT
        );
    }
}
