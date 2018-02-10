<?php
/**
 * Created by PhpStorm.
 * User: dizzylee
 * Date: 2017/7/8
 * Time: 下午6:41
 */

namespace Home\Controller;
use Think\Controller;


class DepartmentController extends HomeController
{
    public function createDepartment()
    {
        try
        {
            $this->jsonReturn(D('Department')->createDepartment(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function editDepartment()
    {
        try
        {
            $this->jsonReturn(D('Department')->editDepartment(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function getDepartment()
    {
        try
        {
            $this->jsonReturn(D('Department')->getDepartment(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function deleteDepartment()
    {
        try
        {
            $this->jsonReturn(D('Department')->deleteDepartment(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

}