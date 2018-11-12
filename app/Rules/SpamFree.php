<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/23
 * Time: 10:55
 */

namespace App\Rules;

use App\Inspections\Spam;

class SpamFree
{
    public function passes($attribute, $value)
    {
        try{
            return ! resolve(Spam::class)->detect($value);
        }catch (\Exception $e){
            return false;
        }
    }
}