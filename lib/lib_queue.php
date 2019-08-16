<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 5/25/2019
 * Time: 12:04 PM
 * Note: lib_queue.php
 */

namespace app\tests\lib;

use ext\redis_queue;

class lib_queue extends redis_queue
{
    public $tz = 'start,child';

    /**
     * lib_queue constructor.
     *
     * @throws \RedisException
     */
    public function __construct()
    {
        $this->connect();
    }
}