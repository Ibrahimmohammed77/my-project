<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Providers\CustomUserProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\AuthServiceInterface;
use App\Services\AuthService;
use App\Services\NotificationService;
use App\Services\StorageService;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Services
        $this->app->singleton(AuthServiceInterface::class, function ($app) {
            return new AuthService(
                $app->make(UserRepositoryInterface::class),
                $app->make(NotificationService::class)
            );
        });

        $this->app->singleton(NotificationService::class);
        $this->app->singleton(StorageService::class);

        // Register repositories
        $this->registerRepositories();

        // Register helpers
        $this->registerHelpers();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom user provider
        $this->registerCustomUserProvider();

        // Register custom validation rules
        $this->registerCustomValidationRules();

        // Set default string length
        Schema::defaultStringLength(191);

        // Configure performance settings
        $this->configurePerformance();

        // Register observers
        $this->registerObservers();

        // Register query builder macros
        $this->registerQueryBuilderMacros();
    }

    /**
     * Register custom user provider.
     */
    protected function registerCustomUserProvider(): void
    {
        Auth::provider('custom', function ($app, array $config) {
            return new CustomUserProvider(
                $app['hash'],
                $config['model'],
                $app->make(UserRepositoryInterface::class)
            );
        });
    }

    /**
     * Register custom validation rules.
     */
    protected function registerCustomValidationRules(): void
    {
        Validator::extend('yemeni_phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(009677|9677|\\+9677|07)([0-9]{8})$/', $value);
        }, 'رقم الهاتف غير صالح. يجب أن يكون رقم هاتف يمني.');

        Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/', $value);
        }, 'كلمة المرور يجب أن تحتوي على حرف كبير وصغير ورقم ورمز خاص.');

        Validator::extend('arabic_text', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[\p{Arabic}\s]+$/u', $value);
        }, 'يجب أن يحتوي الحقل على نص عربي فقط.');

        Validator::extend('english_text', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-zA-Z\s]+$/', $value);
        }, 'يجب أن يحتوي الحقل على نص إنجليزي فقط.');

        Validator::extend('username_format', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-zA-Z0-9_]+$/', $value);
        }, 'اسم المستخدم يجب أن يحتوي على حروف إنجليزية وأرقام وشرطة سفلية فقط.');

        Validator::extend('image_dimensions', function ($attribute, $value, $parameters, $validator) {
            if (!$value instanceof \Illuminate\Http\UploadedFile) {
                return false;
            }

            [$minWidth, $minHeight, $maxWidth, $maxHeight] = array_pad($parameters, 4, null);

            $dimensions = getimagesize($value->getPathname());
            $width = $dimensions[0];
            $height = $dimensions[1];

            if ($minWidth && $width < $minWidth) return false;
            if ($minHeight && $height < $minHeight) return false;
            if ($maxWidth && $width > $maxWidth) return false;
            if ($maxHeight && $height > $maxHeight) return false;

            return true;
        }, 'أبعاد الصورة غير صالحة.');
    }

    /**
     * Register helpers.
     */
    protected function registerHelpers(): void
    {
        $helperFiles = glob(app_path('Helpers') . '/*.php');

        foreach ($helperFiles as $helperFile) {
            require_once $helperFile;
        }
    }

    protected function registerRepositories(): void
    {
        // User Repository
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        // Album Repository
        $this->app->bind(
            \App\Repositories\Contracts\AlbumRepositoryInterface::class,
            \App\Repositories\AlbumRepository::class
        );

        // Storage Repository
        $this->app->bind(
            \App\Repositories\Contracts\StorageRepositoryInterface::class,
            \App\Repositories\StorageRepository::class
        );

        // Payment Repository
        $this->app->bind(
            \App\Repositories\Contracts\PaymentRepositoryInterface::class,
            \App\Repositories\PaymentRepository::class
        );
    }

    /**
     * Configure performance settings.
     */
    protected function configurePerformance(): void
    {
        // Increase memory limit
        ini_set('memory_limit', '256M');

        // Increase execution time
        ini_set('max_execution_time', 300);

        // Increase upload size
        ini_set('upload_max_filesize', '50M');
        ini_set('post_max_size', '50M');

        // Database performance settings
        config([
            'database.connections.mysql.options' => [
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_TIMEOUT => 30,
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'",
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ]);

        // Session configuration for performance
        config([
            'session.lottery' => [2, 100], // Cleanup old sessions 2% of requests
            'session.expire_on_close' => false,
            'session.cookie_lifetime' => 43200, // 30 days in minutes
        ]);
    }

    /**
     * Register observers.
     */
    protected function registerObservers(): void
    {
        $models = [
            \App\Models\User::class => \App\Observers\UserObserver::class,
            \App\Models\Album::class => \App\Observers\AlbumObserver::class,
            \App\Models\Photo::class => \App\Observers\PhotoObserver::class,
            \App\Models\Card::class => \App\Observers\CardObserver::class,
            \App\Models\Invoice::class => \App\Observers\InvoiceObserver::class,
            \App\Models\Subscription::class => \App\Observers\SubscriptionObserver::class,
        ];

        foreach ($models as $model => $observer) {
            if (class_exists($model) && class_exists($observer)) {
                $model::observe($observer);
            }
        }
    }

    /**
     * Register query builder macros.
     */
    protected function registerQueryBuilderMacros(): void
    {
        // Search in multiple columns
        \Illuminate\Database\Query\Builder::macro('whereLike', function ($columns, $search) {
            $this->where(function ($query) use ($columns, $search) {
                foreach ((array) $columns as $column) {
                    $query->orWhere($column, 'LIKE', "%{$search}%");
                }
            });

            return $this;
        });

        // Active scope macro
        \Illuminate\Database\Eloquent\Builder::macro('active', function () {
            return $this->where('is_active', true);
        });

        // With common relations macro
        \Illuminate\Database\Eloquent\Builder::macro('withCommonRelations', function () {
            return $this->with([
                'status:id,code,name',
                'type:id,code,name',
                'roles:id,name',
            ]);
        });

        // Where date between
        \Illuminate\Database\Eloquent\Builder::macro('whereDateBetween', function ($column, $startDate, $endDate) {
            return $this->whereDate($column, '>=', $startDate)
                ->whereDate($column, '<=', $endDate);
        });
    }
}
