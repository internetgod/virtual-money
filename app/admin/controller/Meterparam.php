<?php
/**
 * Created by PhpStorm.
 * User: hawk2fly
 * Date: 2017/9/19
 * Time: 下午1:55
 */

namespace app\admin\controller;

use think\Log;
use think\Loader;

class MeterParam extends Admin
{

    public function index(){
        return view();
    }

    /**
     * 异步获取列表数据
     *
     * @author chengbin
     * @return mixed
     */
    public function getList()
    {
        if(!request()->isAjax()) {
            $this->error(lang('Request type error'), 4001);
        }
        $request = request()->param();
        $request['company_id'] = $this->company_id;
        $data = model('MeterParam')->getList( $request );
        $total = model('MeterParam')->getTotalMeterParamNumber(['company_id' => $this->company_id]);
        return json(["total" => $total,"rows" => $data]);
    }

    /**
     * 添加
     */
    public function add()
    {
        return $this->fetch('edit');
    }

    /**
     * 编辑
     * @param  string $id 数据ID（主键）
     */
    public function edit($id = 0)
    {
        if(empty($id)){
            return info(lang('Data ID exception'), 0);
        }
        $MeterParam = model('MeterParam')->where(['id' => $id])->find();
        $this->assign('data', $MeterParam);
        return $this->fetch();
    }

    /**
     * 保存数据
     * @param array $data
     *
     * @author chengbin
     */
    public function saveData()
    {
        if(!request()->isAjax()) {
            return info(lang('Request type error'));
        }

        $data = input('post.');
        $data['company_id'] = $this->company_id;
        if(empty($data['id'])){
            if(model('MeterParam')->findInfo(['company_id'=>$this->company_id])){
                $this->error("运营参数只能添加一条！");
            }
            unset($data['id']);
        }
        if( !model('MeterParam')->saveData( $data ) ){
            Log::record(['添加运行参数失败' => model('MeterParam')->getError(),'data' => $data],'error');
            $this->error(model('MeterParam')->getError());
        }
        model('LogRecord')->record( 'Edit MeterParam',$data );
        $this->success(lang('Save success'));
    }

    /**
     * 删除
     * @param  string $id 数据ID（主键）
     */
    public function delete($id = 0){
        if(empty($id)) {
            return info(lang('Data ID exception'), 0);
        }
        $meterParams = model('MeterParam')->getMeterParamsById($id,$this->company_id);
        if( count($meterParams) != count(explode(',',$id)) ){
            Log::record(['删除运行参数失败' => 0,'data' => $id],'error');
            $this->error('操作失败,信息有误');
        }
        if( !model('MeterParam')->deleteById($id) ){
            Log::record(['删除运行参数失败' => model('Role')->getError(),'data' => $id],'error');
            $this->error('操作失败');
        }
        model('LogRecord')->record( 'Delete MeterParam',$id );
        $this->success(lang('Delete succeed'));;
    }
    /**
     * 查询数据
     *
     * @author ducongshu
     */
    public function search(){
        $meter_param = model('MeterParam')->getAllMeterParams(['company_id'=>$this->company_id]);
        $this->assign('meter_param',$meter_param);
        return $this->fetch();
    }

    public function download(){
        $where['company_id'] = $this->company_id;
        $where['delete_time'] = null;
        $arealist = model('Area')->getAreaInfo($where,'select','id,name');
        $this->assign('arealist',$arealist);
        return $this->fetch();
    }

    //指定用户下载
    public function saveDownload(){
        $data = input('data');
        $data = json_decode($data,true);
        $ret['code'] = 200;
        $ret['msg'] = lang('Operation Success');
        try{
            if( !$data ){
                exception(lang('Operation fail Incomplete Information'),ERROR_CODE_DATA_ILLEGAL);
            }
            if(!$param= model('MeterParam')->findInfo(['company_id'=>$this->company_id])){
                exception(lang('Please Create Meterdata First'),ERROR_CODE_DATA_ILLEGAL);
            }
            $param = $param->toArray();
            if( isset($data['type']) && $data['type'] == 'special_user' ){ //指定用户控制
                if( !isset($data['M_Code']) || !$data['M_Code'] ){
                    exception(lang('Please enter M_Code First'),ERROR_CODE_DATA_ILLEGAL);
                }
                $M_Code= trim($data['M_Code'],';');
                $codes = explode(';',$M_Code);
                $where['M_Code'] = ['in',$codes];
                $where['company_id'] = $this->company_id;
                $where['meter_life'] = METER_LIFE_ACTIVE;
                $where['meter_status'] = METER_STATUS_BIND;
                $meters = model('Meter')->columnInfo($where,'M_Code');
                $diff = array_diff($codes,$meters);
                if(!empty($diff)){
                    $ret['data'] = $diff;
                    exception(lang('These M_Code are incorrect! Please enter again'),300);
                }
                $meter_data = model('Meter')->selectInfo($where,'id,M_Code,P_ID');
            }elseif( isset($data['type']) && $data['type'] == 'area_user' ){
                if( !isset($data['area_id']) || !$data['area_id'] ){
                    exception(lang('Please choose Area First'),ERROR_CODE_DATA_ILLEGAL);
                }
                if(!model('Area')->selectInfo(['id'=>$data['area_id'],'company_id'=>$this->company_id],'id')){
                    exception(lang('You have no right to the area for this operation'),ERROR_CODE_DATA_ILLEGAL);
                }
                $where['company_id'] = $this->company_id;
                $where['meter_life'] = METER_LIFE_ACTIVE;
                $where['meter_status'] = METER_STATUS_BIND;
                $where['M_Address'] = $data['area_id'];
                if(!$meter_data= model('Meter')->selectInfo($where,'id,M_Code,P_ID')){
                    exception(lang('There are no downloadable M_Code'),ERROR_CODE_DATA_ILLEGAL);
                }
            }elseif(isset($data['type']) && $data['type'] == 'all_user'){
                if( !isset($data['radio_type']) || $data['radio_type']!=1 ){
                    exception(lang('Data Illegal'),ERROR_CODE_DATA_ILLEGAL);
                }
                $where['company_id'] = $this->company_id;
                $where['meter_life'] = METER_LIFE_ACTIVE;
                $where['meter_status'] = METER_STATUS_BIND;
                if(!$meter_data= model('Meter')->selectInfo($where,'id,M_Code')){
                    exception(lang('There are no downloadable M_Code'),ERROR_CODE_DATA_ILLEGAL);
                }
            }else{
                exception(lang('Unlawful choice'),ERROR_CODE_DATA_ILLEGAL);
            }

            $result=$this->addTask($meter_data,$param);
            if(!empty($result)){
                $ret['data'] = $result;
                exception(lang('The following M_Code number is not downloaded successfully, and the rest has been downloaded'),500);
            }
            $meter_data = array_map(function($item){return $item->toArray();},$meter_data);
            Loader::model('Logrecord')->record('Download Meterparam',$meter_data);
        }catch (\Exception $e){
            $ret['code'] = $e->getCode() ? $e->getCode() : ERROR_CODE_DEFAULT;
            $ret['msg'] = $e->getMessage();
        }
        return $ret;
    }

    //添加task；
    private function addTask($datas,$param){
        $arrs=[];
        foreach($datas as $key=> $value){
            $task['meter_id'] = $value['id'];
            $task['cmd'] = 'downloadMeterparam';
            $task['param'] = $param;
            $ret = upsertTask($task);
            if(is_array($ret)){
                $arrs[$key]['code']=$value['M_Code'];
                $arrs[$key]['reason']=$ret['msg'];
            }
        }
        return $arrs;
    }
}