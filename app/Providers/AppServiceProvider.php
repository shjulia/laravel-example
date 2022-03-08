<?php

namespace App\Providers;

use App\Channels\SmsChannel;
use App\Http\Validation\ReCaptcha;
use App\Jobs\Analytics\SendErrorsJob;
use App\Listeners\Shift\ProvidersNotFoundListener;
use App\Listeners\Shift\ShiftUpdateListener;
use App\Listeners\Shift\Support\ProviderRequestedListener;
use App\Repositories\Invite\InviteRepository;
use App\Repositories\Payment\ProviderBonusesRepository;
use App\Repositories\Payment\ProviderChargeRepository;
use App\Services\ImageAnalysis\CompareFaces;
use App\Services\Integration\CoreService;
use App\Services\Maps\AutocompletePlaceService;
use App\Services\Maps\DistanceService;
use App\Services\Maps\GeocodeService;
use App\Services\Maps\PlaceService;
use App\Services\Maps\TimeZoneService;
use App\UseCases\Background\Provider\ChargeUpdaterService;
use Aws\Rekognition\RekognitionClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Mailgun\Mailgun;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*if (App::environment('production')) {
            //$this->emailLog();
        }*/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $file = app_path('Helpers/helpers.php');
        if (file_exists($file)) {
            require_once($file);
        }
        $this->app->bind(RekognitionClient::class, function (Application $app) {
            $config = $app['config']['filesystems']['disks']['s3'];
            return new RekognitionClient([
                'version' => 'latest',
                'region' => $config['region'],
            ]);
        });
        $this->app->bind(CompareFaces::class, function (Application $app) {
            $config = $app['config']['filesystems']['disks']['s3'];
            return new CompareFaces($app->get(RekognitionClient::class), $config['bucket']);
        });

        $this->app->bind(DistanceService::class, function (Application $app) {
            $config = $app['config']['services']['map'];
            return new DistanceService(new Client(), $config['gmap_api_key']);
        });

        $this->app->bind(GeocodeService::class, function (Application $app) {
            $config = $app['config']['services']['map'];
            return new GeocodeService(new Client(), $config['gmap_api_key']);
        });

        $this->app->bind(TimeZoneService::class, function (Application $app) {
            $config = $app['config']['services']['map'];
            return new TimeZoneService(new Client(), $config['gmap_api_key']);
        });

        $this->app->bind(PlaceService::class, function (Application $app) {
            $config = $app['config']['services']['map'];
            return new PlaceService(new Client(), $config['gmap_api_key']);
        });

        $this->app->bind(ReCaptcha::class, function (Application $app) {
            $config = $app['config']['services']['re_captcha'];
            return new ReCaptcha(new Client(), $config['secret_key']);
        });

        $this->app->bind(AutocompletePlaceService::class, function (Application $app) {
            $config = $app['config']['services']['map'];
            return new AutocompletePlaceService(new Client(), $config['gmap_api_key']);
        });

        $this->app->bind(Mailgun::class, function (Application $app) {
            $config = $app['config']['services']['mailgun'];
            return new Mailgun($config['key']);
        });

        $this->app->bind(ProviderRequestedListener::class, function (Application $app) {
            $config = $app['config']['app'];
            return new ProviderRequestedListener(array_filter([
                $config['ceo_email'] ?: null,
                $config['developer_email'] ?: null,
                $config['manager_email'] ?: null,
                $config['manager2_email'] ?: null,
                $config['manager3_email'] ?: null
            ]));
        });

        $this->app->bind(ProvidersNotFoundListener::class, function (Application $app) {
            $config = $app['config']['app'];
            return new ProvidersNotFoundListener(array_filter([
                $config['ceo_email'] ?: null,
                $config['developer_email'] ?: null,
                $config['manager_email'] ?: null,
                $config['manager3_email'] ?: null
            ]));
        });

        $this->app->bind(ShiftUpdateListener::class, function (Application $app) {
            $config = $app['config']['app'];
            return new ShiftUpdateListener(array_filter([
                $config['ceo_email'] ?: null,
                $config['developer_email'] ?: null,
                $config['manager_email'] ?: null,
                $config['manager3_email'] ?: null
            ]));
        });

        $this->app->bind(SmsChannel::class, function (Application $app) {
            $config = $app['config']['services']['sms'];
            return new SmsChannel($config['app_key'], $config['app_secret'], $config['from']);
        });
        $this->app->bind(CoreService::class, function (Application $app) {
            return new CoreService(
                new Client(),
                env('BOON_CORE_URL_BASE'),
                env('BOON_CORE_LOGIN'),
                env('BOON_CORE_PASSWORD'),
                $app->get(LoggerInterface::class)
            );
        });
    }
}
