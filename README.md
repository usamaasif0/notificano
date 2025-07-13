# Notificano 

**Notificano** is a modern, open source, developer-friendly real-time notification system for Laravel. Built to leverage Laravel Reverb, Notificano is among the first (and most complete) open source packages to offer plug-and-play real-time notifications for Laravel applications. It provides a seamless, flexible, and robust solution for adding notification bells, unread counts, and notification lists to your app—without the hassle of building everything from scratch.

- **First-class real-time notifications:** Instantly notify users of new events, messages, or updates using Laravel Reverb.
- **Easy integration:** Add a notification bell and list to your layout with a single Blade directive.
- **Flexible and customizable:** Publish and override views, config, and assets as needed.
- **Production-ready:** Built for Laravel 10/11+, with best practices and extensibility in mind.
- **Open source & community-driven:** Licensed under MIT, Notificano welcomes issues, suggestions, and contributions from the Laravel community.

> **Notificano is one of the first comprehensive, open source real-time notification packages for Laravel using Reverb.**

---

## Features
- Real-time notifications (Laravel Reverb)
- Notification bell with unread count
- Notification list and mark-as-read functionality
- Publishable migrations, config, and assets
- Customizable via config
- Simple Blade directive: `@notificanoBell`

---

## Installation

### 1. Require the Package (Local Path Example)

Add to your `composer.json`:

```
"repositories": [
  { "type": "path", "url": "packages/notificano" }
]
```

Then run:

```
composer require usamaasif0/notificano:dev-main
```

### 2. Publish Migrations, Config, and Assets

```
php artisan notificano:publish-migrations
```

### 3. Run Migrations

```
php artisan migrate
```

---

## User Model Setup

Add the following method to your `User` model (`app/Models/User.php`) to enable notification relationships:

```php
public function notifications()
{
    return $this->hasMany(\Notificano\Models\Notification::class, 'to_user');
}
```

---

## Usage

### 1. Add the Notification Bell to Your Layout

**Blade Directive (recommended):**
```blade
@notificanoBell
```

### 2. Configuration

Publish and edit `config/notificano.php` to adjust settings (e.g., max notifications shown):
```php
return [
    'max_notifications' => 5,
];
```

---

## Customization
- You can override the published views in your app's `resources/views/vendor/notificano/` directory.
- Change the default avatar image by replacing `public/images/no_avatar.webp`.

---

## Troubleshooting
- **Notifications table missing?** Run migrations after publishing.
- **Not seeing the bell?** Make sure you are logged in and have included `@notificanoBell` in your layout.
- **Auth issues?** Ensure your routes use both `web` and `auth` middleware.
- **Custom user model?** The package auto-detects your user model via Laravel config.

---

## License
MIT

---

## Contributing

Notificano is open source and welcomes contributions! If you have ideas, bug reports, or want to help improve the package, please open an issue or submit a pull request on GitHub.

### Coding Standards & Contribution Guidelines

- **Code Style:** Please follow [PSR-12](https://www.php-fig.org/psr/psr-12/) for all PHP code. Use camelCase for variables and methods, StudlyCase for classes, and UPPER_SNAKE_CASE for constants.
- **Autoloading:** All classes and files should follow [PSR-4](https://www.php-fig.org/psr/psr-4/) autoloading standards. Place classes in the correct namespace and directory structure.
- **Testing:** If possible, add or update tests for your changes.
- **Code Quality:** Run code style checks and tests before submitting a pull request.
- **Pull Requests:** Clearly describe your changes and reference any related issues.

Thank you for helping make Notificano better! 