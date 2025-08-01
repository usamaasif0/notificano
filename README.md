# Notificano 

**Notificano** is a modern, open source, developer-friendly real-time notification system for Laravel. Built to leverage Laravel Reverb, Notificano is among the first (and most complete) open source packages to offer plug-and-play real-time notifications for Laravel applications. It provides a seamless, flexible, and robust solution for adding notification bells, unread counts, and notification lists to your app—without the hassle of building everything from scratch.

- **First-class real-time notifications:** Instantly notify users of new events, messages, or updates using Laravel Reverb.
- **Easy integration:** Add a notification bell and list to your layout with a single Blade directive.
- **Flexible and customizable:** Publish and override views, config, and assets as needed.
- **Production-ready:** Built for Laravel 11 and above, with best practices and extensibility in mind.
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

## View Examples

**Notification Bell Example:**

<div align="center">
  <img src=".github/images/bell_notification.png" alt="Notification Bell Example" />
</div>


**Notification List Page Example:**

<img src=".github/images/notifications_page.png" alt="Notification List Page Example" />

## Prerequisites

Before installing Notificano, make sure your Laravel app has:

- **Laravel Reverb** installed and configured for real-time broadcasting.
- Broadcasting set up and working.

If you have not set up broadcasting and Reverb, run:

```sh
php artisan install:broadcasting
```

Follow the prompts to complete the setup. For more details, see the [Laravel Reverb documentation](https://laravel.com/docs/11.x/reverb).

---

## Running Reverb and Frontend Assets

To enable real-time notifications, you must run the Reverb server:

```sh
php artisan reverb:start
```

If your project uses a frontend build system (like Vite or Laravel Mix), make sure to install dependencies and run the dev server:

```sh
npm install
npm run dev
```

Keep both the Reverb server and your frontend dev server running during development for real-time updates.

---

## Installation

### 1. Install the Package

Run the command:

```
composer require usamaasif0/notificano
```

### 2. Publish All Resources
Run:
```
php artisan vendor:publish --tag=notificano-all
```

### 3. Publish and Run Migrations
Run:
```
php artisan notificano:publish-migrations
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

### 3. Sending Notifications Programmatically

You can send a notification from anywhere in your app using the `setNotification` helper:

- **First parameter:** An array of notification data (must include at least a 'title', can optionally include a 'url').
- **Second parameter:** The user ID to send the notification to (optional, defaults to 1 if not provided).

```php
setNotification(
    ['title' => 'string (required)', 'url' => 'route name (optional)'],
    $toUserId // optional, defaults to user ID 1
);
```
- `title`: The notification title (required)
- `url`: The route name or URL (optional)
- `toUserId`: The user ID to send the notification to (optional, defaults to 1)

### 4. Use Case Example

Here is an example of how you might use `setNotification` in a controller when a new post is created:

```php
use Illuminate\Http\Request;

public function store(Request $request)
{
    // ... your logic to create a resource, e.g., a new post
    $post = Post::create($request->all());

    // Send a notification to user with ID 5
    setNotification([
        'title' => 'A new post was created!',
        'url' => route('posts.show', $post->id) // optional
    ], 5);

    // ... rest of your controller logic
}
```

**Explanation:**
- The first parameter is an array with at least a `'title'` key. You can also include `'url'` or any other data your notification system uses.
- The second parameter is the user ID you want to notify (in this example, user ID 5). If you omit it, the notification will be sent to user ID 1 by default.

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
