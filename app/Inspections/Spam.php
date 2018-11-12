<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/23
 * Time: 9:40
 */

namespace App\Inspections;

class Spam
{
    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDowm::class,
    ];

    public function detect($body)
    {
        foreach ($this->inspections as $inspection){
            app($inspection)->detect($body);
        }

        return false;
    }
}