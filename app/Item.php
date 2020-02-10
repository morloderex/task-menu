<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Item extends Model
{
    use NodeTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'field',
        'menu_id'
    ];

    /**
     * @param $id
     * @return int
     */
    public static function getDepth($id)
    {
        $menu = Menu::findOrFail($id);

        $item = static::scoped(['menu_id' => $menu->getKey()])->withDepth();

        $items = $item->cursor();

        return (int) $items->pluck('depth')->max() ?: 0;
    }

    /**
     * @return string[]
     */
    protected function getScopeAttributes()
    {
        return ['menu_id'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
