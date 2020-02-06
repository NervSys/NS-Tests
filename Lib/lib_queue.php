<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 5/25/2019
 * Time: 12:04 PM
 * Note: lib_queue.php
 */

namespace app\UnitTest\Lib;

use ext\queue;
use ext\redis;

class lib_queue extends queue
{
    public $tz = [
        'go',
        'unit'
    ];

    public function __construct()
    {
        parent::__construct(redis::new()->connect());
    }
}