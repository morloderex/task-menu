<?php

namespace Tests\Feature;

use App\Menu;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateMenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_menu()
    {
        $response = $this->postJson('/api/menus', [
            'field' => 'My awesome menu'
        ]);

        $response->assertStatus(201);

        $this->assertCount(1, Menu::all());
        $this->assertEquals('My awesome menu', Menu::first()->field);
        $this->assertNull(Menu::first()->max_children);
        $this->assertNull(Menu::first()->max_depth);

        $response->assertExactJson([
            'field' => 'My awesome menu',
            'max_depth' => null,
            'max_children' => null
        ]);
    }

    /** @test */
    public function field_data_is_required()
    {
        $response = $this->postJson('/api/menus');

        $response->assertJsonValidationErrors('field');

        $this->assertSame(0, Menu::count());
    }

    /**
     * @test
     */
    public function it_can_specify_a_max_depth()
    {
        $response = $this->postJson('/api/menus', [
            'field' => 'My awesome menu',
            'max_depth' => 5
        ]);

        $response->assertCreated();

        $this->assertCount(1, Menu::all());
        $this->assertEquals(5, Menu::first()->max_depth);

        $response->assertExactJson([
            'field' => 'My awesome menu',
            'max_depth' => 5,
            'max_children' => null
        ]);
    }

    /**
     * @test
     */
    public function it_can_specify_a_max_children()
    {
        $response = $this->postJson('/api/menus', [
            'field' => 'My awesome menu',
            'max_children' => 5
        ]);

        $response->assertCreated();

        $this->assertCount(1, Menu::all());
        $this->assertEquals(5, Menu::first()->max_children);

        $response->assertExactJson([
            'field' => 'My awesome menu',
            'max_depth' => null,
            'max_children' => 5
        ]);
    }

    /**
     * @test
     * @dataProvider validationErrorsForField
     */
    function field_data_must_be_a_string($data)
    {
        /**
         * @var $response \Illuminate\Foundation\Testing\TestResponse
         */
        $response = $this->postJson('/api/menus', $data);

        $response->assertJsonValidationErrors('field');

        $this->assertSame(0, Menu::count());
    }

    /**
     * @test
     * @dataProvider ValidationDataForMaxDepth
     */
    public function max_depth_must_be_an_unsigned_integer($data)
    {
        $response = $this->postJson('/api/menus', $data);

        $response->assertJsonValidationErrors(['max_depth']);
        $response->assertJsonMissingValidationErrors('field');

        $response->assertStatus(422);

        $this->assertSame(0, Menu::count());
    }

    /**
     * @test
     * @dataProvider ValidationDataForMaxChildren
     */
    public function max_children_must_be_an_unsigned_integer($data)
    {
        $response = $this->postJson('/api/menus', $data);

        $response->assertJsonValidationErrors(['max_children']);
        $response->assertJsonMissingValidationErrors(['field', 'max_depth']);


        $response->assertStatus(422);

        $this->assertSame(0, Menu::count());
    }

    public function validationErrorsForField()
    {
        return [
            [['field' => true]],
            [['field' => 1]],
            [['field' => 2.5]],
            [['field' => [
                'invalid' => 'key'
            ]]]
        ];
    }

    public function ValidationDataForMaxDepth()
    {
        return [
            [['field' => 'some data', 'max_depth' => 'some-string']],
            [['field' => 'some data', 'max_depth' => true]],
            [['field' => 'some data', 'max_depth' => -1]],
            [['field' => 'some data', 'max_depth' => [
                'testing' => 'test'
            ]]]
        ];
    }

    public function ValidationDataForMaxChildren()
    {
        return [
            [['field' => 'some data', 'max_children' => 'some-string']],
            [['field' => 'some data', 'max_children' => true]],
            [['field' => 'some data', 'max_children' => -1]],
            [['field' => 'some data', 'max_children' => [
                'testing' => 'test'
            ]]]
        ];
    }
}
