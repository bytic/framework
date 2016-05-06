<?php

class Nip_Auths extends Records
{

	/**
	 * @return Nip_Auth
	 */
	public function getCurrent()
    {
        $model = $this->getModel();
        if ($_SESSION[$model]) {
            $item = $this->findOne($_SESSION[$model]['id']);
            if ($item) {
                $item->authenticated(true);
            }
            $this->_current = $item;
        } else {
            $this->_current = $this->getNew();
        }

        return $this->_current;
    }

}