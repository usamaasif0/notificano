<?php

namespace Notificano;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Notificano\Models\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

class NotificanoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            \Notificano\Console\PublishMigrationsCommand::class,
        ]);
        $this->mergeConfigFrom(
            __DIR__ . '/../config/notificano.php', 'notificano'
        );
    }

    public function boot(): void
    {
        $max_notifications = config('notificano.max_notifications', 5);
        View::composer('notificano::notifications.partials.bell-notification', function ($view) use ($max_notifications) {
            try {
                if (auth()->check() && Schema::hasTable('notifications')) {
                    $to_user = auth()->id();

                    $unreadCount = Notification::where('to_user', $to_user)
                        ->whereNull('read_at')
                        ->count();

                    $unreadNotifications = Notification::with(['fromUser:id,name,avatar'])
                        ->where('to_user', $to_user)
                        ->latest('created_at')
                        ->limit($max_notifications)
                        ->get(['id', 'title', 'url', 'read_at', 'created_at', 'from_user']);

                    $view->with([
                        'initialUnreadCount' => $unreadCount,
                        'unreadNotifications' => $unreadNotifications,
                        'max_notifications' => $max_notifications,
                    ]);
                } else {
                    $view->with([
                        'initialUnreadCount' => 0,
                        'unreadNotifications' => collect(),
                        'max_notifications' => $max_notifications,
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::error('Notification view composer error: ' . $e->getMessage());
                $view->with([
                    'initialUnreadCount' => 0,
                    'unreadNotifications' => collect(),
                    'max_notifications' => $max_notifications,
                ]);
            }
        });

        Blade::directive('notificanoBell', function () {
            return "<?php echo view('notificano::notifications.partials.bell-notification')->render(); ?>";
        });

        // Load helpers
        if (file_exists(__DIR__ . '/Helpers/helpers.php')) {
            require_once __DIR__ . '/Helpers/helpers.php';
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'notificano');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/channels.php');

        $timestamp1 = date('Y_m_d_His');
        $timestamp2 = date('Y_m_d_His', time() + 1);
        $this->publishes([
            __DIR__ . '/../database/migrations/create_notifications_table.stub' => database_path('migrations/' . $timestamp1 . '_create_notifications_table.php'),
            __DIR__ . '/../database/migrations/add_avatar_to_users_table.stub' => database_path('migrations/' . $timestamp2 . '_add_avatar_to_users_table.php'),
            __DIR__ . '/../resources/images/no_avatar.webp' => public_path('images/no_avatar.webp'),
            __DIR__ . '/../config/notificano.php' => config_path('notificano.php'),
            __DIR__ . '/../resources/views' => resource_path('views/vendor/notificano'),
        ], 'notificano-all');
    }
}
