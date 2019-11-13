<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 5/25/2019
 * Time: 12:04 PM
 * Note: lib_queue.php
 */

namespace app\tests\lib;

use ext\queue;

class lib_queue extends queue
{
    public $tz = [
        'start',
        'unit'
    ];
}