<?php
/**
 * JWT(Json Web Token) 管理
 *
 * @author    Alex Xun xunzhibin@expert.com
 * @version   1.0
 * @copyright (C) 2019 Jnexpert Ltd. All rights reserved
 * @file      ..\application\common\token\JsonWebToken.php
 */

namespace app\common\token;

use Firebase\JWT\JWT;

/**
 * JWT(Json Web Token) 基础类
 *
 * 详细说明区(长描述)
 *
 * @author Alex Xun xunzhibin@jnexpert.com
 * @package common
 */
class JsonWebToken
{
    /**
     * 生成 JWT
     *
     * 将PHP数组转换并签名为JWT字符串
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $options 有效载荷相关数据数组
     *
     * @return stirng
     */
    public function encode($options)
    {
        // 有效载荷
        $payload = [
            'iss' => 'jnexpert.com',
            'aud' => 'jnexpert.com',
            'jti' => $options['tokenId'],
            // 'data' => $options['data']
        ];
        if(isset($options['data'])) {
            $payload['data'] = $options['data'];
        }

        // 私密密钥
        $key = 'jnexpert.com.key';

        // 签名算法
        $signAlg = 'HS256';

        // 生成
        $jwt = JWT::encode($payload, $key, $signAlg);

        return $jwt;
    }

    /**
     * 解析 JWT
     *
     * 将JWT字符串解码为PHP数组
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param string $jwt JWT字符串
     *
     * @return array
     */
    public function decode($jwt)
    {
        // 检查 时钟偏差(秒), 把时间留点余地
        JWT::$leeway = 60;

        // 私密密钥
        $key = 'jnexpert.com.key';

        // 签名算法
        $signAlg = 'HS256';

        // 解码
        $object = JWT::decode($jwt, $key, [$signAlg]);

        $payload = (array)$object;

        return [
            'tokenId' => $payload['jti'],
            'data' => isset($payload['data']) ? (array)$payload['data'] : []
        ];
    }
}
