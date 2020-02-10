<?php

namespace Tests\Feature;

use App\Item;
use App\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuDepthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function depth_returns_0_when_no_items_has_been_created_to_the_menu()
    {
        $menu = factory(Menu::class)->create();

        $response = $this->getJson('/api/menus/' . $menu->getKey(). '/depth');

        $response->assertOk();
        $response->assertExactJson([
            'depth' => 0
        ]);
    }

    /**
     * @test
     */
    public function depth_returns_1_when_1_depth_has_been_reached_on_the_menu()
    {
        $menu = factory(Menu::class)->create();

        $item = Item::create([
            'menu_id' => $menu->getKey(),
            'field' => 'some-field'
        ]);

        /** @var Item $item2 */
        $item2 = Item::make([
            'menu_id' => $menu->getKey(),
            'field' => 'some-field'
        ]);

        $item2->parent()->associate($item);

        $item2->save();

        $response = $this->getJson('/api/menus/' . $menu->getKey(). '/depth');

        $response->assertOk();
        $response->assertExactJson([
            'depth' => 1
        ]);
    }

    /**
     * @test
     */
    public function depth_returns_404_when_getting_depth_for_a_menu_that_does_not_exists()
    {
        $response = $this->getJson('/api/menus/none-existing-menu/depth');

        $response->assertNotFound();
    }
}
