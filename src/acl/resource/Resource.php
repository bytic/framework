<?php

class ACL_Resource extends Record
{

	/**
	 * @var ACL_Resource
	 */
	protected $_parent;
	protected $_children = array();
	protected $_pathString;

	/**
	 * @param ACL_Resource $resource
	 */
	public function addChild($resource)
	{
		$this->_children[$resource->id] = $resource;
		$resource->setParent($this);
	}

	public function hasParent()
	{
		return $this->id_parent > 0;
	}

	public function hasChildren()
	{
		return count($this->_children) > 0;
	}

	/**
	 * @return ACL_Resource
	 */
	public function getParent()
	{
		if (!$this->_parent) {
			if ($this->hasParent()) {
				$parent = $this->getManager()->findOne($this->id_parent);
				$parent->addChild($this);
			}
		}

		return $this->_parent;
	}

	public function getChildren()
	{
		return $this->_children;
	}

	public function getPath()
	{
		return $this->getManager()->buildPath($this);
	}

	public function getPathString()
	{
		if (!$this->_pathString) {
			$this->_pathString = implode(".", Nip_Helper_Array::instance()->pluck($this->getPath(), "slug"));
		}

		return $this->_pathString;
	}

	/**
	 * @param ACL_Resource $parent
	 * @return ACL_Resource
	 */
	public function setParent(ACL_Resource $parent)
	{
		$this->_parent = $parent;
		return $this;
	}

	public function getACL()
	{
		return ACL::instance();
	}

	public function insertPermission($role, $type)
	{
		return $this->getACL()->insertPermission($this, $role, $type);
	}

	public function removePermissions()
	{
		return $this->getACL()->removePermissions(false, $this);
	}

}