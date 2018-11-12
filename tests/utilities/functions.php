<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/10
 * Time: 15:21
 */

function create($class, $attributes = [], $times = null)
{
    return factory($class, $times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}

function raw($class,$attributes = [], $times = null)
{
    return factory($class, $times)->raw($attributes);
}