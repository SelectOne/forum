<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/23
 * Time: 17:07
 */

namespace App\Exceptions;


class ThrottleException extends \Exception
{
    public function render($request, Exception $exception)
    {
        if($exception instanceof ValidationException){
            return response('Validation failed.',422);
        }

        if($exception instanceof ThrottleException){
            return response('You are posting too frequently.',429);
        }

        return parent::render($request, $exception);
    }
}