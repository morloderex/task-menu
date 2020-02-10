<?php

namespace Tests\Feature;

use App\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowMenuTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_show_a_menu()
    {
        $menu = factory(Menu::class)->create($data = [
            'field' => 'My awesome menu',
            'max_depth' => null,
            'max_children' => null
        ]);

        $response = $this->getJson('/api/menus/' . $menu->getKey());

        $response->assertExactJson($data);
    }

    /**
     * @test
     */
    public function it_cannot_show_a_menu_that_does_not_exist()
    {
        $response = $this->getJson('/api/menus/1');

        $response->assertNotFound();
    }
}
