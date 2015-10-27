<?php
namespace Palm\Utils;

use \Phalcon\DI\Injectable;
use \Phalcon\Mvc\View\Engine\Volt\Compiler;

class ScriptEngine extends Injectable
{
	protected $_compiler = null;
	protected $_functions = array();
	protected $_errors_msg;
	
	public function __construct()
	{
		$this->_compiler = new Compiler();
		$this->_compiler->addExtension($this);
	}

	public function setTmpPath($path)
	{
		$this->tmp_path = rtrim($path, '\/');
	}

	public function getTmpPath()
	{
		return $this->tmp_path;
	}

	public function compile($source_code)
	{
		$mid_script = preg_replace('/([^\r\n]*)([\r\n])?/is', '{% $1 %}$2', $source_code);
		$tmp_file_name = $this->tmp_path.'/'.time();
		$mid_file_path = $tmp_file_name.'.mids';
		$compiled_file_path = $tmp_file_name.'.cpls';
		file_put_contents($mid_file_path, $mid_script);
		try
		{
			$this->_compiler->compileFile($mid_file_path, $compiled_file_path);
		}
		catch (\Phalcon\Mvc\View\Exception $e)
		{
			$msg = $e->getMessage();
			preg_match('/on line (\d+)/', $msg, $matches);
			$this->_error_msg = '脚本第'.$matches[1].'行存在错误';
			return false;
		}
		finally
		{
			unlink($mid_file_path);
		}
		
		$compiled_string = file_get_contents($compiled_file_path);
		unlink($compiled_file_path);
		return preg_replace('/<\?php|\?>/', '', $compiled_string);
	}

	public function getErrorMessage()
	{
		return $this->_error_msg;
	}

	public function compileFunction($func_name, $args)
	{
		$method_name = 'compileFunction'.ucfirst($func_name);
		if(method_exists($this, $method_name))
		{
			return call_user_func(array($this, $method_name), $args);
		}
	}
}