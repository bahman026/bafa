<?php

declare(strict_types=1);

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {
    login();
});

it('can render index page of the category resource.', function () {
    // Act & Assert
    get(CategoryResource::getUrl())->assertOk();
});

it('can list categories in the table.', function () {
    // Arrange
    $categories = Category::factory()->count(5)->create();

    // Act & Assert
    livewire(CategoryResource\Pages\ListCategories::class)
        ->assertCanSeeTableRecords($categories);
});

it('can render edit category page.', function () {
    // Act & Assert
    get(CategoryResource::getUrl('edit', [
        'record' => Category::factory()->create(),
    ]))
        ->assertSuccessful();
});

it('can update category model.', function () {
    // Arrange
    $category = Category::factory()->create();
    $newCategory = Category::factory()->make();
    $imagePath = fake()->imageUrl();
    // Act & Assert
    livewire(CategoryResource\Pages\EditCategory::class, [
        'record' => $category->getRouteKey(),
    ])
        ->fillForm([
            'heading' => $newCategory->heading,
            'slug' => $newCategory->slug,
            'title' => $newCategory->title,
            'content' => $newCategory->content,
            'description' => $newCategory->description,
            'no_index' => $newCategory->no_index,
            'canonical' => $newCategory->canonical,
            'parent_id' => $newCategory->parent_id,
            'status' => $newCategory->status,
            'images' => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($category->refresh())
        ->heading->toBe($newCategory->heading)
        ->slug->toBe($newCategory->slug)
        ->title->toBe($newCategory->title)
        ->content->toBe('<p>' . $newCategory->content . '</p>')
        ->description->toBe($newCategory->description)
        ->no_index->toBe($newCategory->no_index)
        ->canonical->toBe($newCategory->canonical)
        ->parent_id->toBe($newCategory->parent_id)
        ->status->toBe($newCategory->status);
    //    TODO: fix this
    //        ->images->toBe($newCategory->images);
});

it('can create category model.', function () {
    // Arrange
    $newCategory = Category::factory()->make();

    // Act & Assert
    livewire(CategoryResource\Pages\CreateCategory::class)
        ->fillForm([
            'heading' => $newCategory->heading,
            'slug' => $newCategory->slug,
            'title' => $newCategory->title,
            'content' => $newCategory->content,
            'description' => $newCategory->description,
            'no_index' => $newCategory->no_index,
            'canonical' => $newCategory->canonical,
            'parent_id' => $newCategory->parent_id,
            'status' => $newCategory->status,
            'images' => null,
        ])
        ->call('create')
        ->assertHasNoFormErrors();
    $this->assertDatabaseHas(Category::class, [
        'heading' => $newCategory->heading,
        'slug' => $newCategory->slug,
        'title' => $newCategory->title,
        'content' => '<p>' . $newCategory->content . '</p>',
        'description' => $newCategory->description,
        'no_index' => $newCategory->no_index,
        'canonical' => $newCategory->canonical,
        'parent_id' => $newCategory->parent_id,
        'status' => $newCategory->status,
        //TODO: fix this
        //        'images' => null
    ]);
});

it('can delete category model.', function () {
    // Arrange
    $category = Category::factory()->create();

    // Act & Assert
    livewire(CategoryResource\Pages\EditCategory::class, [
        'record' => $category->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);
    $this->assertModelMissing($category);
});