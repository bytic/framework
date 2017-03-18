<?php
namespace Nip\Mvc\Sections;

use Nip\Collections\AbstractCollection;

/**
 * Class Sections
 *
 */
class SectionsManager extends AbstractCollection
{
    protected $currentKey = null;

    /**
     * @return Section
     */
    public function getCurrent()
    {
        return $this->get($this->getCurrentKey());
    }

    /**
     * @return null
     */
    public function getCurrentKey()
    {
        if ($this->currentKey === null) {
            $this->setCurrentKey($this->detectCurrentKey());
        }

        return $this->currentKey;
    }

    /**
     * @param $key
     */
    public function setCurrentKey($key)
    {
        $this->currentKey = $key;
    }

    /**
     * @return string
     */
    public function detectCurrentKey()
    {
        $current = $this->detectFromConstant();
        if (!$current) {
            $current = $this->detectFromSubdomain();
            if (!$current) {
                $current = 'main';
            }
        }

        return $current;
    }

    /**
     * @return bool|string
     */
    public function detectFromConstant()
    {
        return false;
//        return (defined('SPORTIC_SECTION')) ? SPORTIC_SECTION : false;
    }

    /**
     * @return bool|mixed
     */
    public function detectFromSubdomain()
    {
        return request()->getHttp()->getSubdomain();
    }

    /**
     * @return void
     */
    public function init()
    {
        $data = config('sections.sections');
        foreach ($data as $key => $row) {
            $this->set($key, new Section($row->toArray()));
        }
    }
}
