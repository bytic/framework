<?php

namespace Nip;

class Paginator
{
    protected $params;
    protected $itemsPerPage = 10;
    protected $currentPage = 1;
    protected $totalPages;
    protected $count = null;

    /**
     * Returns the offset ant length for limiting results to current page
     * @return array
     */
    public function getLimits()
    {
        $totalPages = $this->getTotalPages();

        $this->currentPage = ($this->currentPage > $totalPages) ? $totalPages : $this->currentPage;

        $dLimit = ($this->currentPage - 1) * $this->itemsPerPage;

        return array($dLimit, $this->itemsPerPage);
    }

    public function getTotalPages()
    {
        $this->totalPages = (int) ($this->count / $this->itemsPerPage);
        if ($this->count % $this->itemsPerPage != 0) {
            $this->totalPages++;
        }
        if ($this->totalPages == 0) {
            $this->totalPages = 1;
        }

        return $this->totalPages;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param array $params
     * @return Nip_Paginator
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param int $count
     * @return Nip_Paginator
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @param int $page
     * @return Nip_Paginator
     */
    public function setCurrentPage($page)
    {
        $this->currentPage = $page ? $page : 1;
        return $this;
    }

    /**
     * @param int $count
     * @return Nip\Paginator
     */
    public function setItemsPerPage($count)
    {
        if ($count) {
            $this->itemsPerPage = $count;
        }
        return $this;
    }
}
