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
     * Token 生成信息
     *
     * @var array
     **/
    protected $options;

    /**
     * 生成信息存储方式
     *
     * @var stirng
     **/
    protected $memoryType;

    /**
     * 初始化
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     */
    public function __construct()
    {
		// 实例化 token服务类
        $this->token = new \app\common\token\JsonWebToken;

		// 默认信息存储方式
        $this->memoryType = Config::get('v1.default_memory_type');
    }

    /**
     * 生成 token
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $data 生成token信息数组
     *
     * @throws \think\exception\DbException
     *
     * @return string
     */
    public function createToken($data)
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
     * @param array $param 解析token信息数组
     *
     * @return array
     */
    public function readToken($param)
    {
        // 解析
        $result = $this->token->decode($param['token']);

        $data = null;
        // 信息存储在token中
        if(isset($result['data'])) {
            $data = $result['data'];
        }

        // 其他方式存储信息
        // if(! $data && Config::get('v1.default_memory_type')) {
        if(! $data && $this->memoryType) {
            // 方法
            // $method = 'getDataFrom' . ucfirst(Config::get('v1.default_memory_type'));
            $method = 'getDataFrom' . ucfirst($this->memoryType);
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
     * @param array $data 生成token信息数组
     *
     * @return string
     */
    protected function touristToken($data)
    {
        // 信息
        $this->options = [
            'data' => [
                't_id' => (int)$data['t_id'],
                'u_id' => (int)$data['u_id'],
            ]
        ];

        // 生成
        $token = $this->encode();
		
		return $token;
    }

    /**
     * 用户访问 生成token
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $data 生成token信息数组
     *
     * @return string
     */
    protected function userToken($data)
    {
        // if(Config::get('v1.default_memory_type')) {
        if($this->memoryType) {
            // 其他方式存储信息
            // $method = 'setDataTo' . ucfirst(Config::get('v1.default_memory_type'));
            $method = 'setDataTo' . ucfirst($this->memoryType);
            $token = $this->$method($data);
        } else {
            // 默认 信息存储在Token中
            $this->options = [
                'data' => [
                    't_id' => (int)$data['t_id'],
                    'u_id' => (int)$data['u_id']
                ]
            ];

            $token = $this->encode();
        }

        return $token;
    }

    /**
     * 信息存储在数据库(database)中
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @throws \think\exception\DbException
     *
     * @param array $param 存储信息数组
     *
     * @return string
     */
    public function setDataToDatabase($param)
    {
        // 实例化模型
        $tokenModel = new Token;

        // 查询后更新, 返回更新后信息
        // check 是否已存在
        $where = [
            [ 'u_id', $param['u_id'] ]
        ];
        $info = $tokenModel->getByWhereUpdate($param, [
            'where' => $where
        ]);

        // 不存在
        if(! $info) {
            // 生成
            $token = $this->encode();

			// 设置数据
            $param['token_id'] = $this->options['tokenId'];
            $param['token'] = $token;

            // 添加
            $result = $tokenModel->add($param);
            if(! $result) {
                throw new \think\exception\DbException('Adding data to DB failed');
            }

            $info['token'] = $token;
        }

        return $info['token'];
    }

    /**
     * 生成 Token
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @return string
     */
    protected function encode()
    {
        // token id
        $snowFlake = new SnowFlake();
        $tokenId = $snowFlake->generate();

        // 生成
        $this->options['tokenId'] = $tokenId;
        $token = $this->token->encode($this->options);

        return $token;
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
    protected function getDataFromDatabase($param)
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
