<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-5-4
 * Time: 下午9:50
 */


/**
 * Class Criteria
 * 该类不属于数据操作的模型类，仅用于描述查询条件及写入数据，较之array则避免了使用array元素之前进行的isset判断
 * 见__get, __set 方法
 */
class Criteria implements ArrayAccess
{
    protected $_collection;

    public function __construct(array $collection=null)
    {
        $this->_collection = (array)$collection;
    }

    public function __get($prop)
    {
        if(!isset($this->_collection[$prop])) return null;
        return $this->_collection[$prop];
    }

    public function __set($prop, $value)
    {
        $this->_collection[$prop] = $value;
    }

    public function __isset($name)
    {
        return isset($this->_collection[$name]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->_collection[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->_collection[$offset]) ? $this->_collection[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if(is_null($offset))
        {
            $this->_collection[] = $value;
        }
        else
        {
            $this->_collection[$offset] = $value;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_collection[$offset]);
    }
}