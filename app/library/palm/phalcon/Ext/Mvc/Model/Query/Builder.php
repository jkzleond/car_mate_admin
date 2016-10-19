<?php
namespace Palm\Phalcon\Ext\Mvc\Model\Query;
use \Phalcon\Mvc\Model\Query\Builder as PhalconBuilder;

class Builder extends PhalconBuilder
{
	public function getQuery()
	{
		$phql = $this->getPhql();

		$dependencyInjector = $this->_dependencyInjector;
		if ( !is_object($dependencyInjector) ) {
			throw new \Exception("A dependency injection object is required to access ORM services");
		}

		/**
		 * Gets Query instance from DI container
		 */
		$query = $dependencyInjector->get(
			"Palm\\Phalcon\\Ext\\Mvc\\Model\\Query",
			[$phql, $dependencyInjector]
		);

		// Set default bind params
		$bindParams = $this->_bindParams;
		if ( is_array($bindParams) ) {
			$query->setBindParams($bindParams);
		}

		// Set default bind params
		$bindTypes = $this->_bindTypes;
		if ( is_array($bindTypes) ) {
			$query->setBindTypes($bindTypes);
		}

		if ( is_bool($this->_sharedLock) ) {
			$query->setSharedLock($this->_sharedLock);
		}

		return $query;
	}
}
