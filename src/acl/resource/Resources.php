<?php

class ACL_Resources extends Records
{

	protected $_sortOn = "name";

	public function findAll()
	{
		if (!$this->getRegistry()->exists("all")) {
			$resources = parent::findAll();
			$this->buildTree($resources);
		}

		return $this->getRegistry()->get('all');
	}

	public function buildTree($resources = array())
	{
		if ($resources) {
			/* @var $resource ACL_Resource */
			foreach ($resources as $resource) {
				if ($resource->hasParent()) {
					$parent = $resources[$resource->id_parent];
					$parent->addChild($resource);
				}
			}
		}
	}

	/**
	 * Recursively builds parents path to passed resource
	 *
	 * @param ACL_Resource|bool $page
	 * @param array $return
	 * @return array
	 */
	public function buildPath($resource, &$return = array())
	{
		if (!($resource instanceof ACL_Resource)) {
			return array_reverse($return);
		}

		$return[] = $resource;

		return $this->buildPath($resource->getParent(), $return);
	}

	/**
	 * Singleton
	 *
	 * @return ACL_Resources
	 */
	public static function instance()
	{
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}

}