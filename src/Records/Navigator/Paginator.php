<?php

use Nip\Database\Query\Select as SelectQuery;

class Nip_Record_Paginator
{
    /**
     * @var SelectQuery
     */
    protected $_query = null;

    protected $_page = 1;
    protected $_itemsPerPage = 20;
    protected $_count;
    protected $_pages;

    /**
     * @param SelectQuery $query
     *
     * @return SelectQuery
     */
    public function paginate($query)
    {
        $query->options('sql_calc_found_rows');
        $query->limit($this->getLimitStart(), $this->getItemsPerPage());

        $this->setQuery($query);

        return $query;
    }

    /**
     * @return SelectQuery
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * @param SelectQuery $query
     */
    public function setQuery($query)
    {
        $this->_query = $query;
    }

    public function count()
    {
        $query = $this->getQuery()->getManager()->newQuery();
        $query->cols('FOUND_ROWS()');

        $result = $query->execute()->fetchResult();

        $this->_count = intval(reset($result));
        $this->_pages = intval($this->_count / $this->_itemsPerPage);

        if ($this->_count % $this->_itemsPerPage != 0) {
            $this->_pages++;
        }

        if ($this->_pages == 0) {
            $this->_pages = 1;
        }
    }

    public function setPage($page = false)
    {
        if ($page) {
            $this->_page = $page;
        }

        return $this;
    }

    public function getPage()
    {
        return $this->_page;
    }

    public function getPages()
    {
        return $this->_pages;
    }

    public function setItemsPerPage($items)
    {
        if ($items > 0) {
            $this->_itemsPerPage = $items;
        }

        return $this;
    }

    public function getItemsPerPage()
    {
        return $this->_itemsPerPage;
    }

    public function getLimitStart()
    {
        return ($this->getPage() - 1) * $this->getItemsPerPage();
    }

    public function getCount()
    {
        return $this->_count;
    }
}
