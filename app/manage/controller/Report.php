<?php
/**
 * Created by PhpStorm.
 * User: ducongshu
 * Date: 2017/10/25
 * Time: 下午15:30
 */

namespace app\manage\controller;

use app\manage\service\CompanyService;
use app\manage\service\MeterService;


class Report extends Admin
{
    //月用量报表；
    public function monthReport(){
        $year       = input('year') ? input('year') : date('Y');
        $companyService = new CompanyService();
        $companys   = $companyService->selectInfo([],'id,company_name');
        $company_id = input('company_id') ? input('company_id') : $companys[0]['id'];
        $company = $companyService->findInfo(['id'=>$company_id],'company_name');
        $where['company_id'] = $company_id;
        $report =getMonthReport($year,$where);
        $this->assign('report',$report);
        $this->assign('companys',$companys);
        $this->assign('year',$year);
        $this->assign('company_id',$company_id);
        $this->assign('company_name',$company['company_name']);
        return view();
    }
    //年用量报表；
    public function yearReport(){
        $startYear = input('startYear') ? input('startYear/d') : date('Y',strtotime('-5 years'));
        $endYear = input('endYear') ? input('endYear/d') : date('Y');
        $companyService = new CompanyService();
        $companys = $companyService->selectInfo([],'id,company_name');
        $company_id = input('company_id') ? input('company_id') : $companys[0]['id'];
        $company = $companyService->findInfo(['id'=>$company_id],'company_name');
        $where['company_id'] = $company_id;
        $res = getYearReport($startYear,$endYear,$where);
        $this->assign('companys',$companys);
        $this->assign('company_id',$company_id);
        $this->assign('company_name',$company['company_name']);
        $this->assign('startYear',$startYear);
        $this->assign('endYear',$endYear);
        $this->assign('report',$res['report']);
        $this->assign('years',$res['years']);
        return view();
    }

    //表具余额；
    public function meterBalance(){
        $M_Code = input('M_Code') ? input('M_Code') : '';
        $companyService = new CompanyService();
        $companys   = $companyService->selectInfo([],'id,company_name');
        $company_id = input('company_id') ? input('company_id') : $companys[0]['id'];
        $where['company_id'] = $company_id;
        $where['M_Code'] = empty($M_Code) ? ['neq',''] : $M_Code ;
        $where['meter_status'] = METER_STATUS_BIND;
        $where['meter_life'] = METER_LIFE_ACTIVE;
        $param['M_Code'] = $M_Code;
        $param['company_id'] = $company_id;
        $meterService = new MeterService();
        $meter = $meterService->getInfoPaginate($where,$param,'detail_address,M_Address,U_ID,M_Code,totalCube,balance');
        $totalbalance = array_sum(array_column($meter->toArray()['data'],'balance'));
        $number = count($meter);
        if($number){
            $savbalance = round($totalbalance/$number,2);
        }else{
            $savbalance = 0 ;
        }
        $this->assign('totalbalance',$totalbalance);
        $this->assign('number',$number);
        $this->assign('savbalance',$savbalance);
        $this->assign('meter',$meter);
        $this->assign('M_Code',$M_Code);
        $this->assign('companys',$companys);
        $this->assign('company_id',$company_id);
        return $this->fetch();
    }

}