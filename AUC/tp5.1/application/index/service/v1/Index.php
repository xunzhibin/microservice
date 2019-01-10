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
use think\facade\Config;
use app\index\model\v1\Token;

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
     * @throws \think\exception\DbException
     *
     * @return string
     */
    public function encode($data)
    {
        // 根据访问角色生成
        if(isset($data['u_id']) && $data['u_id']) {
            // 用户
            $token = $this->userToken($data);
        } else if(isset($data['t_id']) && $data['t_id']) {
            // 游客
            $token = $this->touristToken($data);
        } else {
            // 异常
            throw new \think\exception\ValidateException('Access role not exist');
        }

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

        // 默认 获取token中数据
        $data = $result['data'];

        // 获取数据
        if(Config::get('v1.default_memory_type')) {
            // 方法
            $method = 'getDataFrom';
            $method .= ucfirst(Config::get('v1.default_memory_type'));
            // 获取
            $data = $this->$method($result);
        }

        return $data;
    }

    /**
     * 游客访问 生成token
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $data 数据数组
     *
     * @return string
     */
    protected function touristToken($data)
    {
        // token id
        $snowFlake = new SnowFlake();
        $tokenId = $snowFlake->generate();

        // 生成
        $options = [
            'tokenId' => $tokenId,
            'data' => [
                't_id' => $data['t_id']
            ]
        ];
        $token = $this->token->encode($options);

        return $token;
    }

    /**
     * 用户访问 生成token
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $data 数据数组
     *
     * @throws
     *
     * @return string
     */
    protected function userToken($data)
    {
        // token id
        $snowFlake = new SnowFlake();
        $tokenId = $snowFlake->generate();

        // 实例化模型
        $token = new Token;

        if(Config::get('v1.default_memory_type')) {
            // check 是否已存在
            $where = [
                [ 'u_id', $data['u_id'] ]
            ];
            $result = $token->getByWhereUpdate($data, [
                'where' => $where
            ]);

            if($result) {
                // 已存在
                return $result['token'];
            }

            // 生成
            $options = [
                'tokenId' => $tokenId
            ];
            $token = $this->token->encode($options);

            // 存储数据
            // 方法
            $method = 'addDataTo';
            $method .= ucfirst(Config::get('v1.default_memory_type'));
            // 数据
            $data['tokenId'] = $tokenId;
            $data['token'] = $token;
            // 存储
            if(! $this->$method($data)) {
                throw new \think\exception\DbException('Adding data to DB failed');
            }
        } else {
            // 默认
            // 生成
            $options = [
                'tokenId' => $tokenId,
                'data' => $data
            ];
            $token = $this->token->encode($options);
        }

        return $token;
    }

    /**
     * 将数据添加数据库
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $param 添加数据数组
     *
     * @return int|boot
     */
    protected function addDataToDB($param)
    {
        // 数据
        $addData = [
            'token_id' => $param['tokenId'],
            'token'    => $param['token'],
            't_id'     => $param['t_id'],
            'u_id'     => $param['u_id']
        ];

        // 实例化模型
        $token = new Token;

        // 添加
        $result = $token->add($addData);

        return $result;
    }

    /**
     * 从数据库中查询数据
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $param 查询参数数组
     *
     * @return array
     */
    protected function getDataFromDB($param)
    {
        // 实例化模型
        $token = new Token;

        // 查询
        $result = $token->getByPk($param['tokenId']);

        if(! $result) {
            // 不存在
            return $result;
        }

        return [
            't_id' => $result['t_id'],
            'u_id' => $result['u_id']
        ];
    }
}
