<?php

class ACL_Role extends Record
{

	public function insert()
	{
		parent::insert();
		$this->insertPermissions();
	}

	public function update($permissions = true)
	{
		if ($permissions) {
			$this->updatePermissions();
		}
		parent::update();
	}

	public function delete()
	{
		$this->deletePermissions();
		$this->deleteUsers();
		parent::delete();
	}

	public function insertPermission($resource, $type)
	{
		$this->getACL()->insertPermission($resource, $this, $type);
	}

	public function insertPermissions()
	{
		if ($this->permissions) {
			foreach ($this->permissions as $resource => $types) {
				foreach ($types as $type => $value) {
					if (!$this->allowed($resource, $type)) {
						$this->getACL()->insertPermission($resource, $this, $type);
					}
				}
			}
		}
		return $this;
	}

	public function deletePermissions($resource = false)
	{
		$this->getACL()->removePermissions($this, $resource);
		return $this;
	}

	public function updatePermissions()
	{
		$this->deletePermissions();
		$this->insertPermissions();
	}

	public function allowed($resource, $type)
	{
		return $this->getACL()->check($this, $resource, $type);
	}

	public function getACL()
	{
		return ACL::instance();
	}

	public function getRoleID()
	{
		return get_class($this) . "." . $this->id;
	}

	public function validate($request = array())
	{
		$this->name = clean($request['name']);
		$this->permissions = $request['permission'] ? $request['permission'] : array();

		$errors = array();

		if (!$this->name) {
			$errors['name'] = 'Denumirea este obligatorie';
		}

		$this->errors = $errors;

		if ($this->errors) {
			return false;
		}

		return true;
	}

}