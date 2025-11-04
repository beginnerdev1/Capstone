<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }
    /**
     * Safely get and validate an input (GET or POST) value
     *
     * @param string $key The input key name
     * @param string $method 'get' or 'post' (default: 'get')
     * @param array|null $allowed Optional whitelist of allowed values
     * @param string|null $default Default value if invalid or not provided
     * @return string|null
     */
    protected function safeInput(string $key, string $method = 'get', ?array $allowed = null, ?string $default = null): ?string
    {
        $request = service('request');
        $value = null;

        if ($method === 'post') {
            $value = trim($request->getPost($key) ?? '');
        } else {
            $value = trim($request->getGet($key) ?? '');
        }

        // If a whitelist is provided, ensure the value is allowed
        if ($allowed !== null && !in_array($value, $allowed, true)) {
            return $default ?? null;
        }
        log_message('debug', "Rejected unsafe input: {$key} => {$value}");

        // Remove HTML/JS tags (basic sanitization)
        $value = strip_tags($value);

        // Optionally limit dangerous characters
        $value = preg_replace('/[^\w\s\-@.]/u', '', $value);
        return $value === '' ? $default : $value;
    }
    // pinadaling access sa safeInput para
    //  GET
    protected function g($key, ?array $allowed = null, ?string $default = null): ?string    
    {
        return $this->safeInput($key, 'get', $allowed, $default);
    }
    //  POST
     protected function p($key, ?array $allowed = null, ?string $default = null): ?string    
    {
        return $this->safeInput($key, 'post', $allowed, $default);
    }

}

?>
