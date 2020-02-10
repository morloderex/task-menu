<?php

namespace App\Http\Controllers;

use App\Item;

class MenuDepthController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  mixed  $menu
     * @return array
     */
    public function __invoke($menu)
    {
        return [
            'depth' => Item::getDepth($menu)
        ];
    }
}
