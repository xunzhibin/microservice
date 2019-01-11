<?php
/**
 * 异常处理
 *
 * @author    Alex Xun xunzhibin@expert.com
 * @version   1.0
 * @copyright (C) 2019 Jnexpert Ltd. All rights reserved
 * @file      ..\application\common\exception\Http.php
 */

namespace app\common\exception;

use Exception;
use think\exception\Handle;

/**
 * 异常处理 类
 *
 * @author Alex Xun xunzhibin@jnexpert.com
 * @package common
 */
class Http extends Handle
{
    /**
     * 异常状态码
     *
     * @var int
     **/
    protected $errCode;

    /**
     * 异常文言
     *
     * @var string
     */
    protected $errMsg;

    /**
     * 响应HTTP状态码
     *
     * @var int
     **/
    protected $httpCode;

    /**
     * HTTP 响应异常
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     *
     * @return json
     */
    public function render(Exception $e)
    {
        // var_dump(get_class($e), $e);exit;

        // 异常类 类名(包含命名空间)
        $class = get_class($e);
        // 去除两端反斜线\
        $class = trim($class, '\\');
        // 分割成数组集合
        $classObject = explode('\\', $class);
        // 异常类 类名
        $className = end($classObject);

        // 检查是否存在异常类名方法
        if(method_exists($this, $className)) {
            // 调用异常类名方法
            $this->$className($e);
        } else {
            // 默认
            $this->errCode = 20000009;
            $this->errMsg = 'Server Error';
            $this->httpCode = 500;
        }

        return json([
            'errCode' => $this->errCode,
            'errMsg' => $this->errMsg,
        ], $this->httpCode);



    }

    /**
     * 路由(route)异常
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function RouteNotFoundException($e) {
        $this->errCode = 20000001;
        $this->errMsg = 'Route Not Found';
        $this->httpCode = 404;
    }

    /**
     * 类(class)异常
     *
     * 详细说明区(长描述)
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function ClassNotFoundException($e)
    {
        $this->errCode = 20000002;
        $this->errMsg = 'Class Not Found';
        $this->httpCode = 404;
    }

    /**
     * 请求异常
     *
     * 模块(module)、控制器(controller)、方法(method)不存在，或者非法请求
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function HttpException($e)
    {
        $this->errCode = 20000003;
        $this->errMsg = 'Not Found';
        $this->httpCode = 404;
    }

    /**
     * 参数验证错误
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     *
     */
    protected function ValidateException($e)
    {
        $config = [
            '20000101' => 'Tourist ID is illegal',
            '20000102' => 'User ID is illegal',
            '20000103' => 'Access role not exist',
            '20000104' => 'Access token not exist',
        ];

        $msg = $e->getMessage();
        $code = array_search($msg, $config);
        if(! $code) {
            $code = 20000111;
            $msg = 'Request validate feiled';
        }

        $this->errCode = $code;
        $this->errMsg = $msg;
        $this->httpCode = 422;
    }

    /**
     * PDO 异常
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function PDOException($e)
    {
        $this->errCode = 20000004;
        $this->errMsg = 'Sever Error';
        $this->httpCode = 500;
    }

    /**
     * 数据库处理类 异常
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function DbException($e)
    {
        $this->errCode = 20000005;
        $this->errMsg = 'Sever Error';
        $this->httpCode = 500;
    }

    /**
     * 响应(response) 异常
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function HttpResponseException($e)
    {
        $this->errCode = 20000006;
        $this->errMsg = 'Sever Error';
        $this->httpCode = 500;
    }

    /**
     * 模版(template)异常
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function TemplateNotFoundException($e)
    {
        $this->errCode = 20000007;
        $this->errMsg = 'Sever Error';
        $this->httpCode = 500;
    }

    /**
     * 系统异常
     *
     * PHP系统异常、thinkPHP框架系统异常等
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function ErrorException($e)
    {
        $this->errCode = 20000008;
        $this->errMsg = 'Sever Error';
        $this->httpCode = 500;
    }

    /**
     * Json Web Token 签名异常
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function SignatureInvalidException($e)
    {
        $this->errCode = 20002000;
        $this->errMsg = $e->getMessage();
        $this->httpCode = 422;
    }

    /**
     * Json Web Token 时间异常
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param |Exception $e
     */
    protected function BeforeValidException($e)
    {
        $this->errCode = 20002001;
        $this->errMsg = $e->getMessage();
        $this->httpCode = 422;
    }

    /**
     * Json Web Token 过期
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param \Exception $e
     */
    protected function ExpiredException($e)
    {
        $this->errCode = 20002002;
        $this->errMsg = $e->getMessage();
        $this->httpCode = 422;
    }
}
