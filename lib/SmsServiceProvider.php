<?php

namespace DiagVN;

use DiagVN\Services\Fpt\FptClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository as Config;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ], 'fpt-package-migrations');

        $this->publishes(
            [
                //file source => file destination below
                __DIR__ . '/config/sms.php' => config_path('sms.php'),
                //you can also add more configs here
            ]
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('sms', function () {
            return collect([
                'fpt' => $this->createFptClient($this->app['config'])
            ]);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            FptClient::class
        ];
    }

    /**
     * Create a new Fpt Client.
     *
     * @param Config $config
     *
     * @return FptClient
     *
     */
    protected function createFptClient(Config $config)
    {
        // Check for Fpt config file.
        if (!$this->hasFptConfigSection()) {
            $this->raiseRunTimeException('Missing FPT configuration section.');
        }

        $basicCredentials = null;
        $options = $config->get(['sms.fpt.brand_name', 'sms.fpt.scopes', 'sms.fpt.mode', 'sms.fpt.timeout', 'sms.enable_cache']);

        if ($this->fptConfigHas('client_id')) {
            $basicCredentials = $this->createBasicCredentials(
                $config->get('sms.fpt.client_id'),
                $config->get('sms.fpt.client_secret')
            );
        } else {
            $this->raiseRunTimeException('Please provide FPT API credentials.');
        }

        return new FptClient($basicCredentials, $options);
    }

    /**
     * Checks if has global Fpt configuration section.
     *
     * @return bool
     */
    protected function hasFptConfigSection()
    {
        return $this->app->make(Config::class)
            ->has('sms');
    }

    /**
     * Checks if Fpt config has value for the
     * given key.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function fptConfigHas($key)
    {
        /** @var Config $config */
        $config = $this->app->make(Config::class);

        // Check for Fpt config file.
        if (!$config->has('sms.fpt')) {
            return false;
        }

        return
            $config->has('sms.fpt.' . $key) &&
            !is_null($config->get('sms.fpt.' . $key)) &&
            !empty($config->get('sms.fpt.' . $key));
    }

    /**
     * Create a Basic credentials for client.
     *
     * @param string $key
     * @param string $secret
     *
     * @return string[]
     */
    protected function createBasicCredentials($key, $secret)
    {
        return [
            'client_id' => $key,
            'client_secret' => $secret
        ];
    }

    /**
     * Raises Runtime exception.
     *
     * @param string $message
     *
     * @throws \RuntimeException
     */
    protected function raiseRunTimeException($message)
    {
        throw new \RuntimeException($message);
    }
}
