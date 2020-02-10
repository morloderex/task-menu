<?php

namespace App;

use App\Exceptions\MenuException;
use App\Http\Resources\ItemResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection as BaseCollection;

class Menu extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'field',
        'max_depth',
        'max_children'
    ];

    /**
     * @param $id
     * @return \App\Http\Resources\ItemResource
     */
    public static function buildTree($id)
    {
        $menu = static::findOrFail($id);

        $items = Item::scoped(['menu_id' => $menu->getKey()])->get()->toTree();

        return ItemResource::make($menu->render($items));
    }

    /**
     * @param $id
     * @return void
     */
    public static function deleteItems($id): void
    {
        $menu = static::with('items')->findOrFail($id);

        $menu->items->each(function (Item $item) {
           $item->delete();
        });
    }

    /**
     * @param $items
     * @return Collection
     */
    public function render(Collection $items)
    {
        return $items->map(function ($item) {
            return [
                'field' => $item->field,
                'children' => $this->render($item->children)
            ];
        });
    }

    /**
     * @param array $items
     * @return BaseCollection
     */
    public function createChildren(array $items): BaseCollection
    {
        foreach ($items as $item) {
            DB::transaction(function () use ($item) {
                $menuItem = new Item([
                    'field' => $item['field']
                ]);

                $menuItem->menu()->associate($this);

                if (isset($item['parent'])) {
                    $menuItem = $menuItem->parent()->associate($item['parent']);
                }

                $menuItem->save();
            });
        }

        $items = Item::scoped(['menu_id' => $this->getKey()])
                ->get()->toTree();

        return $this->render($items);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * @param int $count
     */
    public function guardForMaxChildren(int $count): void
    {
        $allowedChildren = $this->max_children;

        if (null === $allowedChildren) {
            return;
        }

        $allowedChildren = (int) $allowedChildren;

        if ($count >= $allowedChildren) {
            throw MenuException::maxChildrenExceeded($allowedChildren);
        }
    }

    /**
     *
     */
    public function guardForMaxDepth(): void
    {
        $currentDepth = Item::getDepth($this->getKey());

        $maxDepth = $this->max_depth;

        if (null === $maxDepth) {
            return;
        }

        if ($currentDepth >= $maxDepth) {
            throw MenuException::maxDepthExceeded($maxDepth);
        }
    }
}
