<?php
/**
 * Created by PhpStorm.
 * User: ling
 * Date: 06/01/2017
 * Time: 9:57 AM
 */

namespace Ling\Controller;


use Common\Logic\BaseController;

class TestController extends BaseController
{
    function testOne(){
        $this->display('testOneView');
    }
    function test_one(){
        $this->ajaxReturn(info());
    }
    function testOneAction(){

    }
}