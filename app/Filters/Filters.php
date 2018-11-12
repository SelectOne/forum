<?php
/**
 * Created by PhpStorm.
 * User: fengwei
 * Date: 2018/10/11
 * Time: 17:01
 */

namespace App\Filters;


use Illuminate\Http\Request;

abstract class Filters
{
    protected $request, $builder;
    protected $filters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilter() as $filter => $value){
//            if (! $this->hasFilter($filter)) return;      //  1.0 重构
//            $this->$filter($this->request->$filter);
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

//    public function hasFilter($filter)                      //  1.0 重构
//    {
//        return method_exists($this, $filter) && $this->request->has($filter);
//    }

    public function getFilter()                             //  2.0 重构
    {
        //   这里不能用only法法
        //   only方法,如果地址栏/threads?by=***时才有效,否则得到的是null
        return array_filter($this->request->only($this->filters));
    }
}