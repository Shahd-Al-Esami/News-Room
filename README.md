
# NewsRoom

NewsRoom is a Laravel-based news publishing application built with clean, modular architecture. It manages users, articles, comments, tags, attachments, and publication workflows while using observers and repository patterns for maintainability.

## Project Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/Shahd-Al-Esami/News-Room.git
   cd News-Room
   ```
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install Node dependencies:
   ```bash
   npm install
   ```
4. Copy the environment file and update settings:
   ```bash
   cp .env.example .env
   ```
   - Configure `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`
   - Configure mail and queue settings if needed
5. Generate application key:
   ```bash
   php artisan key:generate
   ```
6. Run database migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
7. Build front-end assets:
   ```bash
   npm run dev
   ```
8. Start the local development server:
   ```bash
   php artisan serve
   ```

## Main Entities and Relationships

### User
- Represents authenticated users in the system.
- Has roles such as `Writer`, `Admin`, and reader-related access.
- Relationships:
  - `hasOne(Profile)`
  - `hasMany(Article)` as the writer of articles
  - `hasMany(Comment)` for posted comments

### Article
- The core content entity for news articles.
- Attributes include `title`, `content`, `status`, `category`, `slug`, and `published_at`.
- Relationships:
  - `belongsTo(User, writer_id)` as the author
  - `morphMany(Comment)` for comments attached to the article
  - `morphMany(Attachment)` for file attachments
  - `morphToMany(Tag)` for tags applied to the article
- Additional behavior:
  - `reading_time` computed attribute for estimated read duration
  - `title` is normalized using a mutator

### Comment
- Polymorphic comments that can attach to articles or other commentable entities.
- Relationships:
  - `morphTo()` to the `commentable` parent
  - `belongsTo(User)` as the commenter

### Attachment
- Polymorphic file attachments used by articles and other attachable entities.
- Relationship:
  - `morphTo()` to the `attachable` parent

### Tag
- Reusable tags for categorizing articles.
- Relationship:
  - `morphedByMany(Article, 'taggable')`

## Architecture Decisions

### Repository Pattern

The application uses a repository pattern to separate data access from business logic.

- `App\Repositories\Contracts\BaseRepositoryInterface`
  - Defines base methods: `all`, `find`, `create`, `update`, `delete`
- `App\Repositories\Contracts\ArticleRepositoryInterface`
  - Extends the base interface and adds article-specific methods such as `getPublishedArticles`, `publishArticle`, and `getOldDraftArticles`
- `App\Repositories\Eloquent\BaseRepository`
  - Implements the shared Eloquent logic for common CRUD operations
- `App\Repositories\Eloquent\ArticleRepository`
  - Implements article-specific repository behavior
  - Uses Eloquent queries and custom logic for published articles, drafts, and publishing workflow

The service provider binds the interface to its concrete implementation:
- `App\Providers\RepositoryServiceProvider`
  - Binds `ArticleRepositoryInterface` to `ArticleRepository`

This design improves testability, dependency inversion, and future maintainability.

### Observer Pattern

The application uses an observer to centralize article lifecycle behavior.

- `App\Observers\ArticleObserver`
  - Observes `Article` model events such as `created`, `updated`, `deleted`, `restored`, and `forceDeleted`
  - Clears cache tags for `Articles` and `Users` when article state changes
- `App\Providers\AppServiceProvider`
  - Registers the observer with `Article::observe(ArticleObserver::class)`

Using an observer keeps the model event handling outside of controllers and models, making cache invalidation and side effects easier to manage.

## Key Features

- Article publishing workflow with `draft` and `published` statuses
- Polymorphic comments and attachments
- Tagging system for articles
- Cached model lifecycle event handling using an observer
- Repository layer for article persistence and business rules
- Role-aware user and article relationships

## Notes

- Ensure `.env` is configured correctly before running migrations.
- If you need to support queues or notifications, run the queue worker separately:
  ```bash
  php artisan queue:work
  ```

This README is tailored for the NewsRoom project and documents the architecture, setup, and core patterns used in the codebase.
'@; Set-Content -Path README.md -Value $content -Encoding UTF8
<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
