<?php
/**
 * Created by PhpStorm.
 * User: ducongshu
 * Date: 2017/10/26
 * Time: 下午14:12
 */

namespace app\manage\service;

use app\manage\model\MeterModel;

class MeterService extends BasicService
{

    public function __construct(){
        $this->dbModel = new MeterModel();
    }
}