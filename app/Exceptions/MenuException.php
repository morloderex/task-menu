<?php

namespace App\Exceptions;

use LogicException;

class MenuException extends LogicException
{

    public static function maxChildrenExceeded($allowedChildren): self
    {
        return new self("Cannot create any more items the maximum allowed items is: $allowedChildren");
    }

    public static function maxDepthExceeded($maxDepth)
    {
        return new self("Cannot create any more items as with this depth as it exceeds the maximum depth allowed: {$maxDepth}");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'message' => $this->message
        ], 422);
    }
}
