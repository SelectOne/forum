<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/23
 * Time: 10:18
 */

namespace App\Inspections;

use Exception;

class KeyHeldDowm
{
    /**
     * 检测连续按键
     * @param $body
     * @throws Exception
     */
    public function detect($body)
    {
        if(preg_match('/(.)\\1{4,}/',$body)){
            throw new Exception('Your reply contains spam.');
        }
    }
}