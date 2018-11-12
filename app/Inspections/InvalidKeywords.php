<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/23
 * Time: 10:17
 */

namespace App\Inspections;

use Exception;

class InvalidKeywords
{
    protected $keywords = [
        'something forbidden'
    ];

    /**
     * 检测关键字
     * @param $body
     * @throws Exception
     */
    public function detect($body)
    {
        foreach ($this->keywords as $invalidKeyword){
            if(stripos($body,$invalidKeyword) !== false){
                throw new Exception('Your reply contains spam.');
            }
        }
    }
}