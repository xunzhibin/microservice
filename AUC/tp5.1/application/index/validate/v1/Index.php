<?php
/**
 * 鉴权中心 验证管理
 *
 * 详细说明区
 *
 * @author    Alex Xun xunzhibin@expert.com
 * @version   1.0
 * @copyright (C) 2019 Jnexpert Ltd. All rights reserved
 * @file      ..\application\index\validate\v1\Index.php
 */

namespace app\index\validate\v1;

use think\Validate;

/**
 * 鉴权中心 验证类
 *
 * 详细说明区(长描述)
 *
 * @author Alex Xun xunzhibin@jnexpert.com
 * @package validate
 */
class Index extends Validate
{
    /**
     * 验证规则
     *
     * @var array
     **/
    protected $rule = [
        't_id' => 'number', // 游客(tourist)
        'u_id' => 'number', // 用户(user)
        'token' => 'must', // 访问令牌
    ];

    /**
     * 错误文言
     *
     * @var array
     */
    protected $message = [
        // 不合法的游客ID
        't_id.number' => 20000101,
        // 不合法的用户ID
        'u_id.number' => 20000102,
        // 访问令牌不存在
        'token.must'  => 20000104,
    ];

    /**
     * token 生成场景验证
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @return object
     */
    public function sceneCreate()
    {
        return $this->only([
            't_id',
            'u_id'
        ]);
    }

    /**
     * token 解析场景验证
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @return object
     */
    public function sceneRead()
    {
        return $this->only([
            'token'
        ]);
    }
}
