
# NewsRoom

This project is a modular and scalable News Management System API built with Laravel. The system supports article publishing, comments, notifications, scheduled reporting, caching, secure file uploads, API versioning, queue processing, and background task handling.

The architecture follows clean separation of concerns using:

Controllers

Services

Repositories

Events & Listeners

Jobs & Queues

Observers

Commands & Scheduler

API Resources


The goal of the project is to provide a maintainable and extensible backend suitable for both web and mobile applications.



## Tech Stack

PHP 8.2+

Laravel 11

MySQL

Redis (recommended for queues/cache/locks)

Laravel Queue System

Laravel Scheduler

 Sanctum Authentication

### Project Setup

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

6. Run migrations

php artisan migrate


---

7. Seed database (optional)

php artisan db:seed


8. Configure queue

Recommended:

QUEUE_CONNECTION=redis

9. Run queue worker:

php artisan queue:work


10. Configure cache

Recommended:

CACHE_STORE=redis


11. Run scheduler locally

php artisan schedule:work

Production cron:

* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1


12. Build front-end assets:
   ```bash
   npm run dev
   ```
13. Start the local development server:
   ```bash
   php artisan serve
   ```

#### Main Entities and Relationships

### User
- Represents authenticated users in the system.
- Has roles such as `Writer`, `Admin`, and 'Reader'.
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
- Attributes include  `body`, `user_id`, `commentable_id`, `commentable_type`.

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

  ### Profile
- Attributes include  `bio`, `user_id`, `phone`, `avatar`, `activity_acore`.
- Relationship:
  - `belongsTo(User)` 
  - `morphOne(Attachment)`





##### Architecture Decisions

### Repository Pattern

The application uses a repository pattern to separate data access from business logic.


Repositories are responsible only for database access.

Why?

To:

isolate queries

reuse database logic

keep services clean

simplify future database changes

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

clearing cache after article updates

triggering side effects automatically 

- `App\Observers\ArticleObserver`
  - Observes `Article` model events such as `created`, `updated`, `deleted`, `restored`, and `forceDeleted`
  - Clears cache tags for `Articles` and `Users` when article state changes
- `App\Providers\AppServiceProvider`
  - Registers the observer with `Article::observe(ArticleObserver::class)`

Using an observer keeps the model event handling outside of controllers and models, making cache invalidation and side effects easier to manage.



### Strategy Pattern + Contextual Binding

The notification system was designed using the Strategy Pattern to support multiple notification delivery mechanisms such as:

- Email notifications
- Database notifications

A shared notification interface was introduced, while each notification type has its own implementation class.

Laravel's IoC Container and Contextual Binding were used to dynamically inject the appropriate notification strategy depending on the service context.

Example:

NotificationServiceInterface
        ↓
EmailNotificationService
DatabaseNotificationService

This approach provides:

- Loose coupling
- Better extensibility
- Cleaner dependency injection
- Easier future expansion

### IoC Container

Laravel's IoC Container was used to manage dependency injection automatically across the application.

Repositories, services, and interfaces are resolved through service container bindings instead of manual instantiation.

This improves:

- Testability
- Maintainability
- Flexibility


### Service Layer

Business logic is handled inside services.

Why?

To:

keep controllers thin

improve maintainability

centralize business rules

simplify testing


Example:

Controller
   ↓
Service
   ↓
Repository



### Events & Listeners

Events are used for decoupled side effects.

Examples:

article published


user registered


Why?

To:

reduce coupling

improve scalability

allow adding features without modifying core logic



### Queues & Jobs

Heavy operations are processed asynchronously.

Examples:

notifications

emails

background processing

report generation


Why?

To:

improve performance

reduce request time

support scalability

## Cache Locks

Cache locks are used to prevent concurrent execution problems.

Example:

Cache::lock('articles-lock')

Why?

To avoid:

duplicate processing

race conditions

cache stampede


## Scheduler & Commands

Scheduled maintenance tasks were implemented using Artisan Commands and Laravel Scheduler.

Examples:

archive old articles

weekly reports


Why?

To automate recurring tasks.

## API Resources

Resources are used to shape API responses.

Why?

To:

separate response formatting from business logic

support multiple API versions cleanly

avoid duplicated transformation logic

## API Versioning

The project supports API versioning:

/api/v1
/api/v2


# Database Indexing

Indexes were added to improve query performance and optimize filtering operations.

Single Indexes

Single indexes were used on frequently searched columns such as:

- email
- status
- published_at

These indexes improve lookup speed for simple queries.

Composite Indexes

Composite indexes were used for queries filtering by multiple columns together.

Example:

(status, published_at)

This improves performance for queries such as:

WHERE status = 'published'
AND published_at >= ?

Using indexes helps reduce query execution time and improves scalability for large datasets.




###### Security Considerations

Implemented security practices include:

Sanctum authentication

secure file uploads

validation rules

authorization checks

mass assignment protection

rate limiting

eager loading optimization



---

Running Useful Commands

Queue Worker

php artisan queue:work


---

Scheduler Worker

php artisan schedule:work


---

Archive Articles

php artisan articles:archive

Custom days:

php artisan articles:archive 60

Dry run:

php artisan articles:archive --dry-run


---

Generate Reports

php artisan send:published-articles-writer-report
 Dry run:
php artisan send:published-articles-writer-report --dry-run


php artisan send:all-articles-published-report
 Dry run:
php artisan send:all-articles-published-report --dry-run





---

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
