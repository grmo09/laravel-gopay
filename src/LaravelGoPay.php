<?php

namespace grmo09\LaravelGoPay;

use GoPay;

/**
 * Class LaravelGoPay
 * @package grmo09\LaravelGoPay
 */
class LaravelGoPay
{
    /** @var mixed $gopay */
    protected $gopay;

    /** @var array $config */
    protected $config = [];

    /** @var array $services */
    protected $services = [];

    /** @var bool $need_init */
    protected $need_init = false;

    /** @var array $logsBefore */
    protected $logsBefore = [];

    /** @var */
    private $logClosure;

    /**
     * LaravelGoPay constructor.
     */
    public function __construct()
    {
        $this->config = [
            'goid'             => config('gopay.go_id'),
            'clientId'         => config('gopay.client_id'),
            'clientSecret'     => config('gopay.client_secret'),
            'isProductionMode' => !filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN),
            'timeout'          => config('gopay.timeout')
        ];

        $fallback = config('app.fallback_locale');

        /** @var string $app_locale */
        $app_locale = app()->getLocale();

        if (isset(config('gopay.languages')[$app_locale])) {
            $language = config('gopay.languages')[$app_locale];
        } else {
            $language = config('gopay.languages')[$fallback];
        }

        if (defined($langConst = 'GoPay\Definition\Language::' . $language)) {
            $this->config['language'] = constant($langConst);
        } else {
            $this->config['language'] = GoPay\Definition\Language::ENGLISH;
        }

        if (defined($scopeConst = 'GoPay\Definition\TokenScope::' . config('gopay.default_scope'))) {
            $this->config['scope'] = constant($scopeConst);
        } else {
            $this->config['scope'] = GoPay\Definition\TokenScope::CREATE_PAYMENT;
        }

        $this->services['cache'] = new LaravelTokenCache();
        $this->services['logger'] = new Logger();

        $this->initGoPay();
    }

    /**
     * @return mixed
     */
    protected function initGoPay()
    {
        $this->gopay = GoPay\Api::payments($this->config, $this->services);

        if ($this->need_init) {
            $this->need_init = false;
        }

        return $this->gopay;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {

            return $this->{$name}(...$arguments);
        } else if (method_exists($this->gopay, $name)) {

            if ($this->need_init) {
                $gp = $this->initGoPay();
            } else {
                $gp = $this->gopay;
            }

            $methodResult = $gp->{$name}(...$arguments);

            return $methodResult;
        }

        return null;
    }

    /**
     * @param $scope
     * @return $this
     */
    public function scope($scope)
    {
        if (defined($scopeConst = 'GoPay\Definition\TokenScope::' . $scope)) {
            $this->config['scope'] = constant($scopeConst);
        } else {
            $this->config['scope'] = $scope;
        }

        $this->need_init = true;

        return $this;
    }

    /**
     * @param $lang
     * @return $this
     */
    public function lang($lang)
    {
        if (defined($langConst = 'GoPay\Definition\Language::' . $lang)) {
            $this->config['language'] = constant($langConst);
        } else if (isset(config('gopay.languages')[$lang]) && defined($langConst = 'GoPay\Definition\Language::' . config('gopay.languages')[$lang])) {
            $this->config['language'] = constant($langConst);
        } else {
            $this->config['language'] = $lang;
        }

        $this->need_init = true;

        return $this;
    }

    /**
     * @param $request
     * @param $response
     */
    public function logHttpCommunication($request, $response)
    {
        if ($this->logClosure == null) {

            $this->logsBefore[] = [$request, $response];
        } else {
            call_user_func($this->logClosure, $request, $response);
        }
    }

    /**
     * @param $closure
     * @return $this
     */
    public function log($closure)
    {
        $this->logClosure = $closure;

        foreach ($this->logsBefore as $log) {
            call_user_func_array($this->logClosure, $log);
        }

        $this->logsBefore = [];

        return $this;
    }
}
