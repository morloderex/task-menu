<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuLayerController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function show($menu)
    {
        // use the menu and layer id to scope the item models to only get the descendants (e.g childs of the item) of the layer id (layer id being a given item id in this case)

        // then use `render` function on Menu model combined with `laravel-nestedset`'s `toTree` function
        // to render the nodes
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($menu)
    {
        // use the menu and layer id to scope the item models to only get the descendants (e.g childs of the item) of the layer id (layer id being a given item id in this case)

        // then resursively call the `delete()` on the Item model in order to delete them.

        // finally return 204 no content response
    }
}
