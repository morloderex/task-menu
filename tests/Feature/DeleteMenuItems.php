<?php

namespace Tests\Feature;

use App\Item;
use App\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteMenuItems extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function it_can_delete_menu_items()
    {
        $menu = factory(Menu::class)->create(['field' => 'a menu to delete']);

        factory(Item::class, 3)->create([
            'field' => $this->faker->word,
            'menu_id' => $menu->getKey()
        ]);

        $response = $this->deleteJson('/api/menus/' .$menu->getKey() . '/items');

        $response->assertNoContent();

        $this->assertDatabaseMissing('items', [
            'menu_id' => $menu->getKey(),
        ]);

        $this->assertDatabaseHas('menu', [
            'field' => 'a menu to delete'
        ]);
    }

    /**
     * @test
     */
    public function it_does_not_delete_other_menus_items()
    {
        $menu = factory(Menu::class)->create(['field' => 'a menu to delete']);
        $menu2 = factory(Menu::class)->create(['field' => 'a second menu to delete']);

        factory(Item::class, 3)->create([
            'field' => $this->faker->word,
            'menu_id' => $menu->getKey()
        ]);

        factory(Item::class, 3)->create([
            'field' => $this->faker->word,
            'menu_id' => $menu2->getKey()
        ]);

        $response = $this->deleteJson('/api/menus/' .$menu->getKey() . '/items');

        $response->assertNoContent();

        $this->assertDatabaseMissing('items', [
            'menu_id' => $menu->getKey(),
        ]);

        $this->assertDatabaseHas('items', [
            'menu_id' => $menu2->getKey(),
        ]);

        $this->assertDatabaseHas('menu', [
            'field' => 'a menu to delete'
        ]);

        $this->assertDatabaseHas('menu', [
            'field' => 'a second menu to delete'
        ]);
    }

    /**
     * @test
     */
    public function it_cannot_delete_menu_items_when_a_menu_is_not_found()
    {
        $response = $this->deleteJson('/api/menus/random-menu/items');

        $response->assertNotFound();
    }
}
