<?php
namespace Palm\Phalcon\Ext\Db\Result;
use \Phalcon\Db\ResultInterface as PhalconResultInterface;

class PdoWrapper implements PhalconResultInterface
{
    protected $_result_cache = null;
	public function __construct($pdoResult)
	{
		$this->_wrapped = $pdoResult;
	}

	public function __get($property)
	{
		return $this->_wrapped->$property;
	}

	public function __call($method, $args)
	{

		$return_value = call_user_func_array(array($this->_wrapped, $method), $args);

		if ($method == 'numRows' or $method == 'fetchAll')
		{
			$this->_wrapped->execute();
		}

		return $return_value;
	}

	public function execute()
	{
		return $this->_wrapped->execute();
	}

	public function fetch($fetchStyle=null, $cursorOrientation=null, $cursorOffset=null)
	{
		return $this->_wrapped->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
	}

	public function fetchArray()
	{
		return $this->_wrapped->fechArray();
	}

	public function fetchAll($fetchStyle=null, $fetchArgument=null, $ctorArgs=null)
	{
	    if ($this->_result_cache)
        {
            return $this->_result_cache;
        }

		$result = $this->_wrapped->fetchAll($fetchStyle, $fetchArgument, $ctorArgs);
        $this->_result_cache = $result;
		return $this->_result_cache;
	}

	public function numRows()
	{
	    $this->fetchAll();
		$result = $this->_wrapped->numRows();
		return $result;
	}

	public function dataSeek($number)
	{
		return $this->_wrapped->dataSeek($number);
	}

	public function setFetchMode($fetchMode, $colNoOrClassNameOrObject=null, $ctorargs=null)
	{
		return $this->_wrapped->setFetchMode($fetchMode, $colNoOrClassNameOrObject, $ctorargs);
	}

	public function getInternalResult()
	{
		return $this->_wrapped->getInternalResult();
	}

}