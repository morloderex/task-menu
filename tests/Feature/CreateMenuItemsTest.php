<?php

namespace Tests\Feature;

use App\Item;
use App\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateMenuItemsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_create_menu_items_and_associate_them_with_a_menu()
    {
        $this->withoutExceptionHandling();

        $menu = factory(Menu::class)->create();

        $response = $this->postJson("/api/menus/{$menu->getKey()}/items", [
            [
                'field' => 'some_value',
            ],
            [
                'field' => 'some-other-value',
            ],
        ]);

        $response->assertCreated();

        $response->assertExactJson([
            [
                'field' => 'some_value',
                'children' => [],
            ],
            [
                'field' => 'some-other-value',
                'children' => [],
            ],
        ]);

        $this->assertEquals(2, Item::count());

        $actual = Item::all()->map(function ($item) {
            return [
                'field' => $item->field,
                'menu_id' => (int)$item->menu_id,
            ];
        })->toArray();

        $expected = [
            [
                'field' => 'some_value',
                'menu_id' => $menu->getKey(),
            ],
            [
                'field' => 'some-other-value',
                'menu_id' => $menu->getKey(),
            ],
        ];

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_create_menu_items_which_has_not_yet_exceeded_the_specified_depth()
    {
        /** @var Menu $menu */
        $menu = factory(Menu::class)->create(['max_depth' => 3, 'max_children' => null, 'field' => 'some menu']);

        $item1 = Item::create([
            'field' => 'item 1',
            'menu_id' => $menu->getKey(),
        ])->refresh();

        /** @var Item $item2 */
        $item2 = Item::make([
            'field' => 'item 2',
            'menu_id' => $menu->getKey(),
        ]);

        $item2->parent()->associate($item1);
        $item2->save();

        $response = $this->postJson('/api/menus/' . $menu->getKey() . '/items', [
            [
                'field' => 'item 3',
                'parent' => $item2->getKey(),
            ],
        ]);

        $response->assertStatus(201);

        //$response->dump();


        $response->assertExactJson([
            [
                'field' => 'item 1',
                'children' => [
                    [
                        'field' => 'item 2',
                        'children' => [
                            [
                                'field' => 'item 3',
                                'children' => [],
                            ],
                        ]
                    ],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_cannot_create_menu_items_which_exceeds_the_specified_depth()
    {
        /** @var Menu $menu */
        $menu = factory(Menu::class)->create(['max_depth' => 1, 'max_children' => null, 'field' => 'some menu']);

        $item1 = Item::create([
            'field' => 'item 1',
            'menu_id' => $menu->getKey(),
        ])->refresh();

        /** @var Item $item2 */
        $item2 = Item::make([
            'field' => 'item 2',
            'menu_id' => $menu->getKey(),
        ]);

        $item2->parent()->associate($item1);
        $item2->save();

        $response = $this->postJson('/api/menus/' . $menu->getKey() . '/items', [
            [
                'field' => 'item 1',
                'parent' => $item2->getKey(),
            ],
        ]);

        $response->assertStatus(422);
        $response->assertExactJson([
            'message' => 'Cannot create any more items as with this depth as it exceeds the maximum depth allowed: 1',
        ]);
    }

    /**
     * @test
     * @dataProvider MaxChildrenProvider
     */
    public function it_cannot_create_menu_items_when_if_max_children_has_been_reached(
        $maxChildren,
        $initialItems,
        $requestedItems,
        $createsItems
    ) {

        //$this->withoutExceptionHandling();
        /** @var Menu $menu */
        $menu = factory(Menu::class)->create(['max_children' => $maxChildren, 'field' => 'some menu']);

        foreach (range(1, $initialItems) as $initialItem) {
            $menu->items()->create([
                'field' => 'item ' . $initialItem,
            ]);
        }

        $requestData = [];

        foreach (range(1, $requestedItems) as $requestItem) {
            $requestData[]['field'] = 'field ' . $requestItem;
        }

        $response = $this->postJson('/api/menus/' . $menu->getKey() . '/items', $requestData);

        if ($createsItems === true) {
            //$response->assertStatus(201);
            $response->assertStatus(201);
            $response->assertJsonStructure([
                '*' => [
                    'field',
                    'children',
                ],
            ]);
            return;
        }

        $response->assertStatus(422);
        $response->assertExactJson([
            'message' => "Cannot create any more items the maximum allowed items is: $maxChildren",
        ]);
    }

    public function maxChildrenProvider()
    {
        return [
            [null, 1, 1, true],
            [3, 1, 3, false],
            [0, 0, 1, false],
            [1, 1, 0, false],
        ];
    }
}
