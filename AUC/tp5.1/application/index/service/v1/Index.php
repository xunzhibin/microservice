<?php
/**
 * 鉴权中心 服务层 管理
 *
 * @author    Alex Xun xunzhibin@expert.com
 * @version   1.0
 * @copyright (C) 2019 Jnexpert Ltd. All rights reserved
 * @file      ..\application\index\service\v1\Index.php
 */

namespace app\index\service\v1;

use generator\snowflake\SnowFlake;
/**
 * 鉴权中心 服务层 基础类
 *
 * @author Alex Xun xunzhibin@jnexpert.com
 * @package service
 */
class Index
{
    /**
     * token 服务对象
     *
     * @var object
     */
    protected $token;

    /**
     * 初始化
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     */
    public function __construct()
    {
        $this->token = new \app\common\token\JsonWebToken;
    }

    /**
     * 生成 token
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param  array $data token相关数据数组
     *
     * @return string
     */
    public function encode($data)
    {
        // token id
        $snowFlake = new SnowFlake();
        $tokenId = $snowFlake->generate();

        $options = [
            'tokenId' => $tokenId,
            'data' => $data
        ];

        // 生成
        $token = $this->token->encode($options);

        return $token;
    }

    /**
     * 解析 token
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $data token相关数据数组
     *
     * @return array
     */
    public function decode($data)
    {
        // 解析
        $result = $this->token->decode($data['token']);

        $data = [];

        if(isset($result['data'])) {
            $data = $result['data'];
        }

        return $data;
    }
}
