<?php

namespace Tests\Feature;

use App\Item;
use App\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowMenuItemsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_show_items_with_a_depth()
    {
        /** @var Menu $menu */
        $menu = factory(Menu::class)->create([
            'max_children' => null,
            'max_depth' => null,
            'field' => 'some menu'
        ]);

        $item = $menu->items()->create([
            'field' => 'item ' . 1
        ])->refresh();

        $item2 = Item::make([
            'field' => 'item 2',
            'menu_id' => $menu->getKey()
        ]);

        $item2->parent()->associate($item);

        $item2->save();

        $response = $this->getJson('/api/menus/' . $menu->getKey() . '/items');

        $response->assertOk();

        $response->assertExactJson([
            [
                'field' => 'item 1',
                'children' => [
                    [
                        'field' => 'item 2',
                        'children' => []
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function it_cannot_show_menu_items_when_a_menu_is_not_found()
    {
        $response = $this->getJson('/api/menus/random-menu/items');

        $response->assertNotFound();
    }
}
