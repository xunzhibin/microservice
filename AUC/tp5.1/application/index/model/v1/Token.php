<?php
/**
 * 访问令牌 模型层 管理
 *
 * @author    Alex Xun xunzhibin@expert.com
 * @version   1.0
 * @copyright (C) 2019 Jnexpert Ltd. All rights reserved
 * @file      ..\application\index\model\Token.php
 */

namespace app\index\model\v1;

use think\Model;
use think\exception\ValidateException;

/**
 * 访问令牌 模型类
 *
 * @author Alex Xun xunzhibin@jnexpert.com
 * @package model
 */
class Token extends Model
{
    /**
     * 当前模型对应的完整数据表名称
     *
     * @var string
     **/
    protected $table = 'access_token';

    /**
     * 默认主键
     *
     * @var string
     **/
    protected $pk = 'token_id';

    /**
     * 是否开启自动写入时间戳字段
     * 开启后, 自动写入create_time和update_time字段值, 值默认为整型(int)
     *
     * @var bool
     **/
    protected $autoWriteTimestamp = true;

    /**
     * 创建时间字段名
     *
     * @var string
     */
    protected $createTime = 'create_time';

    /**
     * 更新时间字段名
     *
     * @var string
     */
    protected $updateTime = 'update_time';

    /**
     * 时间字段显示格式(创建和更新)
     *
     * @var string
     **/
    protected $dateFormat = false;

    /**
     * 只读字段(保护某些特殊字段值不被更改), 一旦写入, 无法更改
     *
     * @var array
     **/
    protected $readonly = [
    ];

    /**
     * 字段类型自动转换, 写入和读取时, 自动进行类型转换处理
     *
     * @var array
     **/
    protected $type = [
        'token_id'    => 'integer', // 令牌ID
        'token'       => 'string', // 令牌
        't_id'        => 'integer', // 游客ID
        'u_id'        => 'integer', // 用户ID
        'expire_time' => 'integer', // 到期时间
    ];

    /**
     * 写入时, 自动完成字段(新增和更新)
     *
     * @var array
     */
    protected $auto = [];

    /**
     * 新增时, 自动完成字段
     *
     * @var array
     **/
    protected $insert = [];

    /**
     * 更新时, 自动完成字段
     **/
    protected $update = [];

    /**
     * 当前DB连接句柄
     *
     * @var object
     */
    protected $dbQuery;

    /**
     * 模型初始化
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     */
    protected static function init()
    {
        // TODO: 初始化
    }

    /**
     * 新增(模型)
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $data 新增数据
     * @param bool $is_batch 批量标识
     *
     * @return int|bool
     */
    public function add($data, $is_batch = false)
    {
        if(! $data) {
			// 无新增数据
            throw new ValidateException('Insert data is NULL');
        }

        // 过滤非数据表字段的数据
        $query = $this->allowField(true);

        if($is_batch) {
            // 批量添加
            $result = $query->saveAll($data, false);
        } else {
            // 单条添加
            $row = $query->save($data);

            // 主键
            $result = $this->{$this->pk};
        }

        return $result;
    }

    /**
     * 更新(模型)
     *
     * 以主键查询信息, 如果存在, 更新变化数据, 返回更新后数据
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $data 更新数据
     *
     * @return bool|array
     */
    public function getByPkUpdate($data)
    {
        // 查询
        $info = $this->get($data[$this->pk]);

        if(! $info) {
            // 不存在
            return false;
        }

        // 设置 数据
        $info->t_id = $data['t_id'];

        // 更新
        $result = $info->save();

        // 返回 更新后数据
        return $info->toArray();
    }

    /**
     * 更新(查询构造器)
     *
     * 以条件查询信息, 如果存在, 更新变化数据, 返回更新后数据
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $data 更新数据
     * @param array $clause SQL子句
     *
     * @return bool|array
     */
    public function getByWhereUpdate($data, $clause)
    {
        $this->dbQuery = $this;

        // 循环设置
        foreach($clause as $method => $row) {
            // 设置条件
            $this->setWhere($method, $row);
        }

        // 查询
        $info = $this->dbQuery->find();
        if(! $info) {
            // 不存在
            return false;
        }

        // 设置 数据
        $info->t_id = $data['t_id'];

        // 更新
        $result = $info->save();

        // 返回 更新后数据
        return $info->toArray();
    }

    /**
     * 查询(模型)
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param int\string|array $pk 主键
     * @param bool $is_batch 批量标识
     *
     * @return array
     */
    public function getByPk($pk, $is_batch = false)
    {
        if($is_batch) {
            // 批量查询
            $result = $this->all($pk);
        } else {
            // 单条查询
            $result = $this->get($pk);
        }

        return $result ? $result->toArray() : $result;
    }

    /**
     * 查询(查询构造器)
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $clause SQL子句
     * @param bool $is_batch 批量标识
     *
     * @return array
     */
    public function getByWhere($clause, $is_batch = false)
    {
        $this->dbQuery = $this;

        // 循环设置
        foreach($clause as $method => $row) {
            // 设置条件
            $this->setWhere($method, $row);
        }

        if($is_batch) {
            // 批量
            $result = $this->dbQuery->select();
        } else {
            // 单条
            $result = $this->dbQuery->find();
        }

        return $result;
    }

    /**
     * 删除(模型)
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param ini|string|array $pk 主键
     *
     * @return bool
     */
    public function removeByPk($pk)
    {
        // 删除
        $result = $this->destroy($pk);

        return $result;
    }

    /**
     * 删除(查询构造器)
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param array $clause SQL子句
     *
     * @return bool
     */
    public function removeByWhere($clause)
    {
        $this->dbQuery = $this;

        // 循环设置
        foreach($clause as $method => $row) {
            // 设置条件
            $this->setWhere($method, $row);
        }

        // 删除
        $sql = $this->dbQuery->delete();

        return true;
    }

    /**
     * 设置 条件
     * AND/OR
     *
     * @author Alex Xun xunzhibin@jnexpert.com
     *
     * @param string $method 查询构造器方法
     * @param array $data 条件信息数组
     */
    protected function setWhere($method, $data)
    {
        foreach($data as $row) {
            // 字段名, 删除数组第一个值, 并返回值
            $field = array_shift($row);
            // 值, 删除数组最后一个值, 并返回值
            $value = array_pop($row);
            // 表达式, 删除数组第一个值, 并返回值
            $op    = $row ? array_shift($row) : '=';

            // 设置
            $this->dbQuery= $this->dbQuery->$method($field, $op, $value);
        }
    }
}
