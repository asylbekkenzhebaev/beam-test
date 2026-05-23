# Beam-Test

## О проекте

`Beam-Test` — это Laravel-приложение для управления небольшим каталогом электроники с упором на ноутбуки.  
Проект включает web-интерфейс на Livewire, REST API и demo-данные для локальной разработки и ручного тестирования.

Сейчас в проекте доступны следующие сущности:
- пользователи
- категории
- товары
- теги

## Стек технологий

- PHP 8.3+
- Laravel 13
- Livewire 4
- Vite 8
- Tailwind CSS 4
- SQLite по умолчанию
- Pest для тестов
- L5 Swagger для API-документации

## Возможности проекта

- web-интерфейс для управления `users`, `categories`, `products`, `tags`
- CRUD-операции без полной перезагрузки страницы через Livewire
- REST API для всех основных сущностей
- Swagger UI для просмотра API-документации
- demo-сиды с данными каталога ноутбуков

## Структура сущностей

- `users`
  Хранит справочные записи пользователей каталога. Авторизация в проекте не используется, поэтому у пользователей нет `password`.
- `categories`
  Категории товаров, например игровые ноутбуки, ультрабуки и аксессуары.
- `products`
  Товары каталога, привязанные к категории.
- `tags`
  Теги для товаров по модели many-to-many.

## Особенности текущей реализации

- `users` не используются для входа в систему и не содержат пароль.
- очереди не используются как прикладная часть проекта
- queue driver по умолчанию: `sync`
- сессии работают через `file`
- demo-данные заполняются отдельными сидерами сущностей
- база данных по умолчанию: `SQLite`

## Требования к окружению

- PHP `8.3+`
- Composer `2+`
- Node.js `18+`
- npm
- SQLite

## Локальный запуск

1. Установить PHP-зависимости:

```bash
composer install
```

2. Создать файл окружения:

```bash
cp .env.example .env
```

3. Сгенерировать ключ приложения:

```bash
php artisan key:generate
```

4. Создать SQLite-файл базы данных, если его еще нет:

```bash
touch database/database.sqlite
```

5. Применить миграции и заполнить demo-данные:

```bash
php artisan migrate --seed
```

6. Установить frontend-зависимости:

```bash
npm install
```

7. Запустить проект в dev-режиме:

```bash
composer dev
```

Команда `composer dev` запускает:
- Laravel server
- `pail` для логов
- Vite dev server

Если нужно, можно запускать вручную:

```bash
php artisan serve
npm run dev
```

## Миграции и сиды

Повторное наполнение проекта demo-данными:

```bash
php artisan migrate:fresh --seed
```

Основной сидер:

```bash
php artisan db:seed
```

Проект использует отдельные сидеры для сущностей:
- `UserSeeder`
- `CategorySeeder`
- `TagSeeder`
- `ProductSeeder`

## Тесты

Запуск всех тестов:

```bash
php artisan test
```

Или через composer:

```bash
composer test
```

## Основные URL

### Web

- `/`
- `/users`
- `/categories`
- `/products`
- `/tags`

### API

- `/api/users`
- `/api/categories`
- `/api/products`
- `/api/tags`

### Документация API

- `/api/documentation`

## Production-развертывание

Ниже базовый универсальный сценарий без привязки к конкретному хостингу.

1. Установить production-зависимости PHP:

```bash
composer install --no-dev --optimize-autoloader
```

2. Подготовить корректный `.env`:
- `APP_ENV=production`
- `APP_DEBUG=false`
- корректный `APP_URL`
- подходящая база данных или SQLite

3. Сгенерировать ключ приложения при первой установке:

```bash
php artisan key:generate
```

4. Подготовить базу данных и применить миграции:

```bash
php artisan migrate --force --seed
```

5. Установить frontend-зависимости и собрать production-ассеты:

```bash
npm install
npm run build
```

6. Закэшировать конфигурацию приложения:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

7. Убедиться, что есть права записи в:
- `storage`
- `bootstrap/cache`

## Полезные команды

```bash
php artisan route:list
php artisan about
php artisan l5-swagger:generate
```

## Примечания

- проект не использует `profiles`
- проект не требует queue worker для базовой работы
- проект не использует database sessions
- в текущем виде основная цель проекта — локальная разработка, демонстрация CRUD-сценариев и API для каталога
