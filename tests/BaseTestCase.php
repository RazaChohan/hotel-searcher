<?php
namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use Libs\CurlHelper;
use PHPUnit\Framework\TestCase;
use Libs\Config;


/***
 * Class BaseTestCase
 *
 * @package tests
 */
class BaseTestCase extends TestCase
{
    /***
     * Application url
     *
     * @var string
     */
    protected $applicationUrl;
    /***
     * Default auth token
     *
     * @var string
     */
    protected $defaultAuthToken;
    /***
     * Curl helper object
     *
     * @var CurlHelper
     */
    public $curlHelper;
    /***
     * Set up to initialize required vars
     */
    public function setUp()
    {
        Config::load('env.php' );
        $this->applicationUrl = Config::get('DEFAULT_APP_URL');
        $this->curlHelper = new CurlHelper();
        $this->defaultAuthToken = Config::get('AUTH_TOKEN');
    }
}