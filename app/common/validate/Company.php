<?php
/**
 * Created by PhpStorm.
 * User: hawk2fly
 * Date: 2017/10/24
 * Time: 下午6:46
 */

namespace app\common\validate;


use think\Validate;

class Company extends Validate
{
    protected $rule =   [
        'company_name'      => 'require',
        'company_type'      => 'require',
        'OPT_ID'            => 'require|unique:company' ,
        'address'           => 'require',
        'quality'           => 'require',
        'contacts_tel'      => 'require',
        'contacts_name'     => 'require',
        'fax'               => 'require',
        'legal_person'      => 'require',
        'bank_name'         => 'require',
        'bank_card'         => 'require',
        'tax_code'          => 'require',
//        'sms_tel'           => 'require',
        'secret_key_url'    => 'require',
//        'secret_key'        => 'require',
        'charge_status'     => 'require',
        'charge_date'       => 'require',
        'limit_times'       => 'require',
        'left_times'        => 'require',
        'percent'           => 'require|between:0,1',
        'charge_limit'      => 'require|gt:0',
//        'desc'              => 'require',
//        'alarm_tel'         => 'require',
//        'status'            => 'require',
    ];

    protected $message  =   [
        'company_name.require'     => '{%Company Name Require}',
        'company_type.require'     => '{%Company Type Require}',
        'OPT_ID.require'           => '{%OPT_ID Require}',
        'OPT_ID.unique'            => '{%OPT_ID MULTI}',
        'address.require'          => '{%Address Require}',
        'quality.require'          => '{%Quality Require}',
        'contacts_tel.require'     => '{%Contacts Tel Require}',
        'contacts_name.require'    => '{%Contacts Name Require}',
        'fax.require'              => '{%Fax Require}',
        'legal_person.require'     => '{%Legal Person Require}',
        'bank_name.require'        => '{%Bank Name Require}',
        'bank_card.require'        => '{%Bank Card Require}',
        'tax_code.require'         => '{%Tax Code Require}',
//        'sms_tel.require'          => '{%SMS Tel Require}',
        'secret_key_url.require'   => '{%Secret Key URL Require}',
//        'secret_key.require'       => '{%Secret Key Require}',
        'charge_status.require'    => '{%Charge Status Require}',
        'charge_date.require'      => '{%Charge Date Require}',
        'limit_times.require'      => '{%Limit Times Require}',
        'left_times.require'       => '{%Left Times Require}',
        'percent.require'          => '{%Percent Require}',
        'percent.between'          => '{%Percent Illegal}',
        'charge_limit.require'     => '{%Charge Limit Require}',
        'charge_limit.gt'         => '{%Charge Limit Illegal}',
//        'desc.require'             => '{%Desc Require}',
//        'alarm_tel.require'        => '{%Alarm Tel Require}',
//        'status.require'           => '{%Status Require}',
    ];

    protected $scene = [
        'add'   => ['company_name','company_type','OPT_ID','address','quality','contacts_tel','contacts_name','fax','legal_person','bank_name','bank_card','tax_code','secret_key_url','charge_status','charge_date','limit_times','left_times','status'],
        'edit'  => ['company_name','company_type','address','quality','contacts_tel','contacts_name','fax','legal_person','bank_name','bank_card','tax_code','secret_key_url','charge_status','charge_date','limit_times','left_times'],
        'del'   => ['status'],
        'setpercent'    => ['percent'],
        'charge'        => ['charge_limit']
    ];
}