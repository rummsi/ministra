<?php

namespace Ministra\Admin\Adapter;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
class DataTableAdapter
{
    const TYPE_EQUALS = '=';
    const TYPE_LIKE = 'like';
    private $request;
    private $skipColumns = array('operations');
    private $draw;
    private $sorted = array();
    private $equalFilters = array();
    private $likeFilters = array();
    private $limit;
    private $offset;
    private $searchValue;
    private $filterColumns;
    private $connection;
    private $processed = false;
    private $havingColumns = array();
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, \Doctrine\DBAL\Connection $connection)
    {
        $this->request = $request;
        $this->connection = $connection;
    }
    public function process()
    {
        $this->limit = $this->request->get('length');
        $this->offset = $this->request->get('start');
        $this->draw = $this->request->get('draw');
        $columns = $this->request->get('columns', []);
        $search = $this->request->get('search');
        $orderColumns = $this->request->get('order', []);
        $equalFilters = $this->request->get('filters', []);
        $this->filterColumns = $this->request->get('filters', []);
        if (\is_array($search) && isset($search['value']) && \mb_strlen($search['value']) > 0) {
            $this->searchValue = $search['value'];
        }
        if (!empty($this->searchValue)) {
            foreach ($columns as $column) {
                if (!isset($column['name']) || \in_array($column['name'], $this->skipColumns)) {
                    continue;
                }
                if (empty($column['name']) || $column['searchable'] === 'false') {
                    continue;
                }
                $this->likeFilters[$column['name']] = $this->searchValue;
            }
        }
        foreach ($orderColumns as $orderColumn) {
            $index = (int) $orderColumn['column'];
            if (!isset($columns[$index])) {
                continue;
            }
            $orderColumnData = $columns[$index];
            if (!isset($orderColumnData['name']) || \in_array($orderColumnData['name'], $this->skipColumns)) {
                continue;
            }
            $this->sorted[$orderColumnData['name']] = $orderColumn['dir'];
        }
        foreach ($equalFilters as $equalFilter => $value) {
            if ($this->checkEmptyValue($value)) {
                continue;
            }
            $this->equalFilters[$equalFilter] = $value;
        }
        $this->processed = true;
        return $this;
    }
    public function getFilters()
    {
        return $this->filterColumns;
    }
    public function setRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->processed = false;
        $this->request = $request;
        return $this;
    }
    public function getOrder()
    {
        return $this->sorted;
    }
    public function getEqualFilters()
    {
        return $this->equalFilters;
    }
    public function getLikeFiters()
    {
        return $this->likeFilters;
    }
    public function getOffset()
    {
        return $this->offset;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function setHavingColumns($havingColumns)
    {
        $this->havingColumns = \is_array($havingColumns) ? $havingColumns : [$havingColumns];
        return $this;
    }
    public function getHavingColumns()
    {
        return $this->havingColumns;
    }
    public function getDrawFlag()
    {
        return $this->draw ?: 1;
    }
    public function generateResponseData($data, $totalFiltered, $total)
    {
        return ['draw' => $this->draw, 'length' => $this->limit, 'start' => $this->offset, 'recordsFiltered' => $totalFiltered, 'recordsTotal' => $total, 'data' => $data];
    }
    protected function checkEmptyValue($value)
    {
        return \mb_strlen("{$value}") === 0 || $value === '<not set>';
    }
}
