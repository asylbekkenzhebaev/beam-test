<?php

use App\Livewire\Categories\Form as CategoryForm;
use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Products\Form as ProductForm;
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\Tags\Form as TagForm;
use App\Livewire\Tags\Index as TagIndex;
use App\Livewire\Users\Form as UserForm;
use App\Livewire\Users\Index as UserIndex;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use App\Services\EntityBroadcastService;
use Livewire\Livewire;

test('dashboard home page is reachable', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee(config('app.name'));
});

test('web crud pages are reachable', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['category_id' => $category->id]);
    Tag::factory()->create();

    $pages = [
        route('users.index'),
        route('categories.index'),
        route('products.index'),
        route('tags.index'),
    ];

    foreach ($pages as $page) {
        $this->get($page)->assertOk();
    }
});

test('user creation broadcasts a pusher notification', function () {
    $broadcasts = Mockery::mock(EntityBroadcastService::class);
    $broadcasts->shouldReceive('broadcastAfterResponse')
        ->once()
        ->withArgs(fn (string $entity, string $action, int $id, string $title, string $message, ?string $url) => $entity === 'user'
            && $action === 'created'
            && $id > 0
            && $title === 'Пользователь создан'
            && str_contains($message, 'Alice')
            && $url === route('users.index'))
        ->andReturn([]);
    $this->app->instance(EntityBroadcastService::class, $broadcasts);

    Livewire::test(UserForm::class)
        ->set('name', 'Alice')
        ->set('email', 'alice@example.com')
        ->call('save')
        ->assertDispatched('user-saved');
});

test('user update broadcasts a pusher notification', function () {
    $user = User::factory()->create([
        'name' => 'Alice',
    ]);

    $broadcasts = Mockery::mock(EntityBroadcastService::class);
    $broadcasts->shouldReceive('broadcastAfterResponse')
        ->once()
        ->withArgs(fn (string $entity, string $action, int $id, string $title, string $message, ?string $url) => $entity === 'user'
            && $action === 'updated'
            && $id === $user->id
            && $title === 'Пользователь обновлен'
            && str_contains($message, 'Alice Updated')
            && $url === route('users.index'))
        ->andReturn([]);
    $this->app->instance(EntityBroadcastService::class, $broadcasts);

    Livewire::test(UserForm::class, ['recordId' => $user->id])
        ->set('name', 'Alice Updated')
        ->set('email', $user->email)
        ->call('save')
        ->assertDispatched('user-saved');
});

test('category with products cannot be deleted', function () {
    $category = Category::factory()->create();
    Product::factory()->create([
        'category_id' => $category->id,
    ]);

    Livewire::test(CategoryIndex::class)
        ->call('delete', $category->id)
        ->assertSet('errorMessage', 'Сначала удалите товары из этой категории или перенесите их в другую.');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
    ]);
});

test('user can be deleted from index component', function () {
    $user = User::factory()->create();

    Livewire::test(UserIndex::class)
        ->call('delete', $user->id)
        ->assertSet('statusMessage', 'Пользователь удален.');

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('product can be deleted from index component', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductIndex::class)
        ->call('delete', $product->id)
        ->assertSet('statusMessage', 'Товар удален.');

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});

test('tag can be deleted from index component', function () {
    $tag = Tag::factory()->create();

    Livewire::test(TagIndex::class)
        ->call('delete', $tag->id)
        ->assertSet('statusMessage', 'Тег удален.');

    $this->assertDatabaseMissing('tags', [
        'id' => $tag->id,
    ]);
});

test('category creation broadcasts a pusher notification', function () {
    $broadcasts = Mockery::mock(EntityBroadcastService::class);
    $broadcasts->shouldReceive('broadcastAfterResponse')
        ->once()
        ->withArgs(fn (string $entity, string $action, int $id, string $title, string $message, ?string $url) => $entity === 'category'
            && $action === 'created'
            && $id > 0
            && $title === 'Категория создана'
            && str_contains($message, 'Hardware')
            && $url === route('categories.index'))
        ->andReturn([]);
    $this->app->instance(EntityBroadcastService::class, $broadcasts);

    Livewire::test(CategoryForm::class)
        ->set('name', 'Hardware')
        ->call('save')
        ->assertDispatched('category-saved');
});

test('category update broadcasts a pusher notification', function () {
    $category = Category::factory()->create([
        'name' => 'Hardware',
    ]);

    $broadcasts = Mockery::mock(EntityBroadcastService::class);
    $broadcasts->shouldReceive('broadcastAfterResponse')
        ->once()
        ->withArgs(fn (string $entity, string $action, int $id, string $title, string $message, ?string $url) => $entity === 'category'
            && $action === 'updated'
            && $id === $category->id
            && $title === 'Категория обновлена'
            && str_contains($message, 'Devices')
            && $url === route('categories.index'))
        ->andReturn([]);
    $this->app->instance(EntityBroadcastService::class, $broadcasts);

    Livewire::test(CategoryForm::class, ['recordId' => $category->id])
        ->set('name', 'Devices')
        ->call('save')
        ->assertDispatched('category-saved');
});

test('product form creates product and syncs tags', function () {
    $category = Category::factory()->create();
    $tags = Tag::factory()->count(2)->create();

    $broadcasts = Mockery::mock(EntityBroadcastService::class);
    $broadcasts->shouldReceive('broadcastAfterResponse')
        ->once()
        ->withArgs(fn (string $entity, string $action, int $id, string $title, string $message, ?string $url) => $entity === 'product'
            && $action === 'created'
            && $id > 0
            && $title === 'Товар создан'
            && str_contains($message, 'Demo Product')
            && $url === route('products.index'))
        ->andReturn([]);
    $this->app->instance(EntityBroadcastService::class, $broadcasts);

    Livewire::test(ProductForm::class)
        ->set('name', 'Demo Product')
        ->set('price', '149.99')
        ->set('category_id', (string) $category->id)
        ->set('tags', $tags->pluck('id')->map(fn (int $id): string => (string) $id)->all())
        ->call('save')
        ->assertDispatched('product-saved');

    $product = Product::query()->where('name', 'Demo Product')->firstOrFail();

    expect($product->category_id)->toBe($category->id);
    expect($product->tags()->pluck('tags.id')->all())->toEqualCanonicalizing($tags->pluck('id')->all());
});

test('product update broadcasts a pusher notification', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Demo Product',
    ]);

    $broadcasts = Mockery::mock(EntityBroadcastService::class);
    $broadcasts->shouldReceive('broadcastAfterResponse')
        ->once()
        ->withArgs(fn (string $entity, string $action, int $id, string $title, string $message, ?string $url) => $entity === 'product'
            && $action === 'updated'
            && $id === $product->id
            && $title === 'Товар обновлен'
            && str_contains($message, 'Updated Product')
            && $url === route('products.index'))
        ->andReturn([]);
    $this->app->instance(EntityBroadcastService::class, $broadcasts);

    Livewire::test(ProductForm::class, ['recordId' => $product->id])
        ->set('name', 'Updated Product')
        ->set('price', '199.99')
        ->set('category_id', (string) $category->id)
        ->set('tags', [])
        ->call('save')
        ->assertDispatched('product-saved');
});

test('tag creation broadcasts a pusher notification', function () {
    $broadcasts = Mockery::mock(EntityBroadcastService::class);
    $broadcasts->shouldReceive('broadcastAfterResponse')
        ->once()
        ->withArgs(fn (string $entity, string $action, int $id, string $title, string $message, ?string $url) => $entity === 'tag'
            && $action === 'created'
            && $id > 0
            && $title === 'Тег создан'
            && str_contains($message, 'featured')
            && $url === route('tags.index'))
        ->andReturn([]);
    $this->app->instance(EntityBroadcastService::class, $broadcasts);

    Livewire::test(TagForm::class)
        ->set('name', 'featured')
        ->call('save')
        ->assertDispatched('tag-saved');
});

test('tag update broadcasts a pusher notification', function () {
    $tag = Tag::factory()->create([
        'name' => 'featured',
    ]);

    $broadcasts = Mockery::mock(EntityBroadcastService::class);
    $broadcasts->shouldReceive('broadcastAfterResponse')
        ->once()
        ->withArgs(fn (string $entity, string $action, int $id, string $title, string $message, ?string $url) => $entity === 'tag'
            && $action === 'updated'
            && $id === $tag->id
            && $title === 'Тег обновлен'
            && str_contains($message, 'sale')
            && $url === route('tags.index'))
        ->andReturn([]);
    $this->app->instance(EntityBroadcastService::class, $broadcasts);

    Livewire::test(TagForm::class, ['recordId' => $tag->id])
        ->set('name', 'sale')
        ->call('save')
        ->assertDispatched('tag-saved');
});

test('user index opens and closes create modal', function () {
    Livewire::test(UserIndex::class)
        ->call('create')
        ->assertSet('showFormModal', true)
        ->assertSet('editingId', null)
        ->call('closeModal')
        ->assertSet('showFormModal', false);
});

test('user index opens edit modal for selected record', function () {
    $user = User::factory()->create();

    Livewire::test(UserIndex::class)
        ->call('edit', $user->id)
        ->assertSet('showFormModal', true)
        ->assertSet('editingId', $user->id);
});

test('user index closes modal after child save event', function () {
    Livewire::test(UserIndex::class)
        ->call('create')
        ->dispatch('user-saved', status: 'Пользователь создан.')
        ->assertSet('showFormModal', false)
        ->assertSet('statusMessage', 'Пользователь создан.');
});

test('invalid user creation does not broadcast a pusher notification', function () {
    $broadcasts = Mockery::mock(EntityBroadcastService::class);
    $broadcasts->shouldNotReceive('broadcastAfterResponse');
    $this->app->instance(EntityBroadcastService::class, $broadcasts);

    Livewire::test(UserForm::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->call('save')
        ->assertHasErrors(['name', 'email']);
});

test('user list shows user data without profile fields', function () {
    User::factory()->create([
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ]);

    $this->get(route('users.index'))
        ->assertOk()
        ->assertSee('Alice')
        ->assertSee('alice@example.com');
});

test('delete buttons use livewire confirm instead of inline wire access', function () {
    User::factory()->create();
    Category::factory()->create();
    Product::factory()->create();
    Tag::factory()->create();

    $this->get(route('users.index'))
        ->assertOk()
        ->assertSee('wire:confirm="Удалить этого пользователя?"', false)
        ->assertDontSee('$wire.delete', false);

    $this->get(route('categories.index'))
        ->assertOk()
        ->assertSee('wire:confirm="Удалить эту категорию?"', false)
        ->assertDontSee('$wire.delete', false);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('wire:confirm="Удалить этот товар?"', false)
        ->assertDontSee('$wire.delete', false);

    $this->get(route('tags.index'))
        ->assertOk()
        ->assertSee('wire:confirm="Удалить этот тег?"', false)
        ->assertDontSee('$wire.delete', false);
});
