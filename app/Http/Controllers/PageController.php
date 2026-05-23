<?php

namespace App\Http\Controllers;

use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\Tags\Index as TagIndex;
use App\Livewire\Users\Index as UserIndex;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    private const PAGES = [
        'users' => ['component' => UserIndex::class, 'title' => 'Пользователи'],
        'categories' => ['component' => CategoryIndex::class, 'title' => 'Категории'],
        'products' => ['component' => ProductIndex::class, 'title' => 'Товары'],
        'tags' => ['component' => TagIndex::class, 'title' => 'Теги'],
    ];

    public function show(Request $request): View
    {
        $resource = (string) $request->route('resource');

        $config = self::PAGES[$resource] ?? null;

        if ($config === null) {
            throw new NotFoundHttpException;
        }

        return view('dashboard.page', [
            'title' => $config['title'],
            'component' => $config['component'],
            'parameters' => [],
        ]);
    }
}
