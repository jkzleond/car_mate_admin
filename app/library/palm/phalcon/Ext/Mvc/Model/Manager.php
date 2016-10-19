<?php
namespace Palm\Phalcon\Ext\Mvc\Model;
use \Phalcon\Mvc\Model\Manager as ModelManager;

class Manager extends ModelManager
{
	public function createBuilder($params=null)
	{
		if ( !is_object($this->_dependencyInjector) ) {
			throw new \Exception("A dependency injection object is required to access ORM services");
		}

		/**
		 * Gets Builder instance from DI container
		 */
		return $this->_dependencyInjector->get("\\Palm\\Phalcon\\Ext\\Mvc\\Model\\Query\\Builder", array($params, $this->_dependencyInjector));
	}
}