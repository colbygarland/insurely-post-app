<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Register markdown blade directive
        Blade::directive('markdown', function ($expression) {
            return "<?php 
                \$converter = new \\League\\CommonMark\\CommonMarkConverter([
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ]);
                echo \$converter->convert($expression)->getContent();
            ?>";
        });

        // Gates
        Gate::define('is-admin', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
