# Beam-Test

## О проекте

`Beam-Test` — это Laravel-приложение для управления каталогом электроники с демо-данными в тематике ноутбуков.  
Проект объединяет:
- web-интерфейс на Livewire
- REST API
- Swagger-документацию
- websocket-уведомления через Pusher

Основная задача проекта — показать работу CRUD, связей между сущностями, API и realtime-обновлений без полной перезагрузки страницы.

## Что есть в проекте

- `users` — пользователи каталога
- `profiles` — вложенные one-to-one профили пользователей
- `categories` — категории товаров
- `products` — товары
- `tags` — теги товаров

### Связи между сущностями

- `User -> Profile` — `one-to-one`
- `Category -> Products` — `one-to-many`
- `Product -> Category` — `many-to-one`
- `Product <-> Tag` — `many-to-many`

## Технологии

- PHP 8.3+
- Laravel 13
- Livewire 4
- Vite 8
- Tailwind CSS 4
- SQLite по умолчанию
- Pest
- L5 Swagger
- Pusher

## Как работает web-часть

### Страницы

В приложении есть одна главная страница и по одной index-странице на основную сущность:

- `/`
- `/users`
- `/categories`
- `/products`
- `/tags`

### CRUD без перезагрузки страницы

Каждая страница сущности работает через Livewire:

- список отображается в `Index`-компоненте
- создание и редактирование происходят в модальном окне
- отдельные маршруты `create` и `edit` не используются
- удаление выполняется прямо из таблицы
- после изменения данные обновляются реактивно

### Пользователи и профили

Пользователь состоит из:
- `name`
- `email`

Профиль пользователя сейчас содержит:
- `phone`

Телефон редактируется прямо в форме пользователя.  
Если телефон пустой, отдельная запись `profile` не создается.

## Как работает API

REST API доступен для основных сущностей:

- `/api/users`
- `/api/categories`
- `/api/products`
- `/api/tags`

API поддерживает стандартные действия:

- `GET /api/{entity}`
- `GET /api/{entity}/{id}`
- `POST /api/{entity}`
- `PUT /api/{entity}/{id}`
- `DELETE /api/{entity}/{id}`

Для `profiles` отдельного API нет.  
Поле `phone` передается как часть `users` API.

### Users API и profile

При создании и обновлении пользователя можно передавать:

```json
{
  "name": "Alice",
  "email": "alice@example.com",
  "phone": "+996 555 123 456"
}
```

В ответе users API возвращается и сам пользователь, и вложенный профиль:

```json
{
  "id": 1,
  "name": "Alice",
  "email": "alice@example.com",
  "phone": "+996 555 123 456",
  "profile": {
    "phone": "+996 555 123 456"
  }
}
```

## Swagger

Swagger UI доступен по адресу:

- `/api/documentation`

Сгенерировать спецификацию вручную можно так:

```bash
php artisan l5-swagger:generate
```

## Как работает websocket

В проекте используется `Pusher`.

### Когда отправляется websocket-событие

Событие отправляется при `create` и `update` сущностей:

- из web-форм Livewire
- из REST API контроллеров

Сейчас websocket используется для:

- `users`
- `categories`
- `products`
- `tags`

Удаление не отправляет websocket-событие.

### Кто отправляет событие

Отправкой занимается сервис:

- [app/Services/EntityBroadcastService.php](/home/note/PhpstormProjects/test/app/Services/EntityBroadcastService.php)

Он формирует payload примерно такого вида:

```json
{
  "entity": "product",
  "action": "created",
  "id": 15,
  "title": "Товар создан",
  "message": "Товар \"ASUS ROG Strix G16\" был создан.",
  "url": "https://example.com/products",
  "timestamp": "..."
}
```

### Важная деталь по производительности

Broadcast не выполняется синхронно в критическом пути формы.  
Сначала сохраняются данные, закрывается модалка и возвращается ответ пользователю, а уже затем событие отправляется через `after response`.

Это сделано для того, чтобы:

- форма не подвисала
- модалка закрывалась сразу
- проблемы сети или Pusher не тормозили UI

### Где websocket принимается

Подписка на Pusher находится во frontend-файле:

- [resources/js/app.js](/home/note/PhpstormProjects/test/resources/js/app.js)

Там происходит:

1. чтение `window.catalogPusherConfig`
2. подключение к `Pusher`
3. подписка на канал каталога
4. получение события `entity.changed`
5. нормализация payload
6. показ toast-уведомления
7. dispatch browser event для Livewire

### Где отображается websocket-сообщение

При получении websocket-события во frontend показывается toast-уведомление в layout.

То есть уведомление видно на любой открытой странице каталога, где подключен основной layout:

- `/`
- `/users`
- `/categories`
- `/products`
- `/tags`

### Как ведет себя toast

Если событие пришло, пользователь видит короткое уведомление:

- заголовок события
- сообщение
- ссылку `Перейти`, если в payload есть `url`

Пример:
- через API создан товар
- на открытых страницах появится toast `Товар создан`

### Обновляется ли таблица автоматически

Да.

После toast фронтенд дополнительно диспатчит browser event:

- `catalog-entity-changed`

Дальше нужный Livewire `Index`-компонент ловит это событие и инициирует refresh списка.

Важно:

- toast показывается на всех страницах каталога
- таблица обновляется только на странице соответствующей сущности

Пример:

- если через API создать `product`
- toast появится и на `/users`, и на `/categories`, и на `/products`, и на `/tags`
- но таблица автоматически обновится только на `/products`

## Demo-данные

В проекте есть отдельные сидеры:

- `UserSeeder`
- `CategorySeeder`
- `TagSeeder`
- `ProductSeeder`

После сидирования создаются:

- 3 пользователя
- 3 профиля пользователей
- 5 категорий
- 10 товаров
- 15 тегов

Тематика данных — электроника и ноутбуки.

## Локальный запуск

1. Установить PHP-зависимости:

```bash
composer install
```

2. Создать `.env`:

```bash
cp .env.example .env
```

3. Сгенерировать ключ:

```bash
php artisan key:generate
```

4. Создать SQLite-файл:

```bash
touch database/database.sqlite
```

5. Применить миграции и demo-данные:

```bash
php artisan migrate --seed
```

6. Установить frontend-зависимости:

```bash
npm install
```

7. Запустить проект:

```bash
composer dev
```

Если нужно вручную:

```bash
php artisan serve
npm run dev
```

## Production-сборка

Собрать production-ассеты:

```bash
npm run build
```

Закэшировать конфигурацию:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Тесты

Запуск всех тестов:

```bash
php artisan test
```

Что покрыто тестами:

- web CRUD
- users profile phone flow
- API CRUD
- Swagger route
- websocket broadcast на create/update
- автообновление таблиц после websocket-событий
- сиды

## Полезные команды

```bash
php artisan route:list
php artisan about
php artisan migrate:fresh --seed
php artisan l5-swagger:generate
php artisan test
```
