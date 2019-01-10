<?php
/**
 * 鉴权中心 控制器层 管理
 *
 * @author    Alex Xun xunzhibin@expert.com
 * @version   1.0
 * @copyright (C) 2019 Jnexpert Ltd. All rights reserved
 * @file      ..\application\index\controller\v1\Index.php
 */

namespace app\index\controller\v1;

use controller\BasicController;
use think\facade\Config;
use app\index\service\v1\Index AS serviceIndex;

/**
 * 鉴权 控制器类
 *
 * @author Alex Xun xunzhibin@jnexpert.com
 * @package controller
 */
class Index extends BasicController
{
    /**
     * 服务层 对象
     *
     * @var object
     **/
    protected $service;

    /**
     * 控制器 初始化
     *
     * 初始化 一些基本属性、参数等
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     */
    protected function initialize()
    {
        // 配置
        $this->config = Config::pull('v1');

        // 父类
        parent::initialize();

        $this->service = new serviceIndex;
    }

    /**
     * 生成 token
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @return array
     */
    public function create()
    {
        $token = $this->service->encode($this->param);

        return [
            'errCode' => 0,
            'errMsg'  => 'OK',
            'token'   => $token
        ];
    }

    /**
     * 解析 token
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @return array
     */
    public function read()
    {
        $data = $this->service->decode($this->param);

        return [
            'errCode' => 0,
            'errMsg'  => 'OK',
            'data'    => $data
        ];
    }
}
