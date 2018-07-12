<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 14:15
 */
return [
        'email'=>[
            'status'=>[
                EMAIL_STATUS_START => '初始状态',
                EMAIL_STATUS_SUCCESS => '成功',
                EMAIL_STATUS_FAIL => '失败',
            ],
            'status_color'=>[
                EMAIL_STATUS_START => 'warning',
                EMAIL_STATUS_SUCCESS => 'success',
                EMAIL_STATUS_FAIL => 'danger',
            ],
            'export_fields' => [
                'email' => '邮箱',
            ]
        ],


];