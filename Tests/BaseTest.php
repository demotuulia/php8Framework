<?php

namespace Tests;

/**
 * A base test for all tests.
 * This does some common functions for all tests
 *
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once  __DIR__ . '/../App/Lib/php8.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Plugins;
use App\Plugins\Db\Db;
use PHPUnit\Framework\TestCase;
use Tests\Traits\THelpers;
use Tests\Traits\TDatabase;
use Tests\Traits\THttpRequest;

class BaseTest extends TestCase
{
    use THelpers;
    use TDatabase;
    use THttpRequest;

    /**
     * @var Db
     */
    protected $db;

    /**
     * Set up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setDatabaseConnection();
        $this->initializeDatabase();
        $this->setTestData();
        parent::setUp();
    }


}