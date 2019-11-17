<?php

namespace Ministra\Admin\Repository;

use Doctrine\DBAL\Connection;
use Ministra\Admin\Adapter\DataTableAdapter;
use Ministra\Lib\SMACCode;
class LicenseKeysRepository
{
    const ACTION_BLOCK = 'block';
    const ACTION_RESERVED = 'reserved';
    const ACTION_ACTIVE = 'active';
    const ACTION_NOT_ACTIVATED = 'not_activated';
    const LICENSE_KEY_TABLE = 'smac_codes';
    protected $connection;
    protected $actionStatuses = array(self::ACTION_ACTIVE => \Ministra\Lib\SMACCode::STATUS_NOT_ACTIVATED, self::ACTION_BLOCK => \Ministra\Lib\SMACCode::STATUS_BLOCKED, self::ACTION_NOT_ACTIVATED => \Ministra\Lib\SMACCode::STATUS_NOT_ACTIVATED, self::ACTION_RESERVED => \Ministra\Lib\SMACCode::STATUS_RESERVED);
    protected $tblName;
    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        $this->connection = $connection;
    }
    public function getTableName()
    {
        return $this->tblName ?: self::LICENSE_KEY_TABLE;
    }
    public function setTableName($tblName)
    {
        $this->tblName = $tblName;
    }
    public function findByPk($pk)
    {
        return $this->connection->createQueryBuilder()->select(['*'])->from($this->getTableName(), 'sc')->where('sc.id = :id')->setParameter('id', $pk)->execute()->fetch();
    }
    public function findByPks($pks)
    {
        if (!$pks) {
            return [];
        }
        $pks = \is_array($pks) ? $pks : [$pks];
        return $this->connection->createQueryBuilder()->select(['*'])->from($this->getTableName(), 'sc')->where('sc.id in (:ids)')->setParameter('ids', $pks, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)->execute()->fetchAll();
    }
    public function findForFilters($groupColumn)
    {
        return $this->connection->createQueryBuilder()->select(['*'])->from($this->getTableName(), 'sc')->orderBy('id', 'asc')->groupBy($groupColumn)->execute()->fetchAll();
    }
    public function getConnection()
    {
        return $this->connection;
    }
    public function findForGrid(\Ministra\Admin\Adapter\DataTableAdapter $dataTableAdapter)
    {
        $query = $this->getBaseQuery()->addSelect(['SUBSTRING(`code`, 2, 1) as key_type', 'u.login as login', 'UNIX_TIMESTAMP(u.last_active) as last_active', 'u.status as user_status'])->leftJoin('sc', 'users', 'u', 'sc.user_id = u.id');
        $whereExpressions = [];
        foreach ($dataTableAdapter->getLikeFiters() as $name => $likeFilter) {
            if (empty($likeFilter)) {
                continue;
            }
            $likeFilter = \utf8_decode($likeFilter);
            $whereExpressions[] = $query->expr()->like($this->getColumnName($name), ":{$name}");
            $query->setParameter($name, "%{$likeFilter}%");
        }
        if (\count($whereExpressions)) {
            $query = $query->andWhere(\call_user_func_array([$query->expr(), 'orX'], $whereExpressions));
        }
        $whereExpressions = [];
        foreach ($dataTableAdapter->getFilters() as $name => $equalFilter) {
            if (empty($equalFilter) && $name !== 'user_status' || $equalFilter === '<not set>') {
                continue;
            }
            $equalFilter = \utf8_decode($equalFilter);
            $parts = \explode(',', $equalFilter);
            $partsExpression = [];
            foreach ($parts as $part => $filter) {
                if ($name === 'user_status') {
                    $filter -= 1;
                }
                if ($name === 'user_status') {
                    $partsExpression[] = $query->expr()->andX($query->expr()->eq($this->getColumnName($name), ":{$name}{$part}"), $query->expr()->isNotNull('u.id'));
                } else {
                    $partsExpression[] = $query->expr()->eq($this->getColumnName($name), ":{$name}{$part}");
                }
                $query->setParameter("{$name}{$part}", \trim($filter));
            }
            $whereExpressions[] = \call_user_func_array([$query->expr(), 'orX'], $partsExpression);
        }
        if (\count($whereExpressions)) {
            $query = $query->andWhere(\call_user_func_array([$query->expr(), 'andX'], $whereExpressions));
        }
        foreach ($dataTableAdapter->getOrder() as $column => $order) {
            $query->addOrderBy($column, $order);
        }
        $query->setMaxResults($dataTableAdapter->getLimit());
        $query->setFirstResult($dataTableAdapter->getOffset());
        return $query;
    }
    public function getGridData($query = null, \Ministra\Admin\Adapter\DataTableAdapter $dataTableAdapter = null)
    {
        $query = $query ?: $this->findForGrid($dataTableAdapter);
        $totalQuery = clone $query;
        $filterBuilder = clone $query;
        $filtered = $filterBuilder->addSelect(['count(sc.id) as cnt'])->setFirstResult(null)->setMaxResults(null)->execute()->fetch();
        return ['total' => $totalQuery->select(['count(sc.id)'])->orderBy('sc.id', 'asc')->resetQueryPart('where')->resetQueryPart('having')->setFirstResult(null)->setMaxResults(null)->execute()->fetchColumn(), 'filter' => $filtered ? $filtered['cnt'] : 0, 'data' => $query->execute()->fetchAll()];
    }
    public function getByPks($ids)
    {
        return $this->connection->createQueryBuilder()->select(['*'])->from($this->getTableName())->where('id in (:pk_list)')->setParameter('pk_list', $ids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY)->execute()->fetchAll();
    }
    protected function getBaseQuery()
    {
        return $this->connection->createQueryBuilder()->select(['sc.id as key_id', 'sc.code as code', 'sc.added as added', 'IF(sc.count_clearing >= 0, sc.count_clearing - ' . 'IF(sc.count_cleared < 0, 0, sc.count_cleared), -1) as count_clear_attempts', 'sc.expire_date as expire_date', 'u.id as user_id', 'sc.device as device', 'sc.status as key_status'])->from($this->getTableName(), 'sc');
    }
    private function getColumnName($name)
    {
        if ($name === 'key_type') {
            return 'SUBSTRING(sc.code, 2, 1)';
        }
        if ($name === 'key_id') {
            return 'sc.id';
        }
        if ($name === 'key_status') {
            return 'sc.status';
        }
        if ($name === 'user_status') {
            return 'u.status';
        }
        if ($name === 'status') {
            return 'sc.status';
        }
        return $name;
    }
    public function findWithActiveUser($ids)
    {
        $query = $this->getConnection()->createQueryBuilder();
        return $query->select(['s.*', 'u.id as user_id, u.status as user_status'])->from('smac_codes', 's')->leftJoin('s', 'users', 'u', 'u.id=s.user_id')->where($query->expr()->in('s.id', \is_array($ids) ? $ids : [$ids]))->execute()->fetchAll();
    }
    public function updateLicensesStatus($actionName, $ids)
    {
        $licenses = $this->findWithActiveUser($ids);
        foreach ($licenses as &$license) {
            $status = $this->getNewStatus($actionName);
            $license['is_updated'] = false;
            if (!$status || (int) $license['user_id'] > 0 && (int) $license['user_status'] === 0) {
                continue;
            }
            $this->connection->createQueryBuilder()->update($this->getTableName(), 's')->set('status', ':status')->set('updated', ':updated')->where('id = :id')->setParameter('status', $status)->setParameter('updated', \date('Y-m-d H:i:s'))->setParameter('id', $license['id'])->execute();
            $license['status'] = $status;
            $license['is_updated'] = true;
            $license['action'] = $status === \Ministra\Lib\SMACCode::STATUS_BLOCKED ? 'active' : $status;
            $license['action'] = $status === \Ministra\Lib\SMACCode::STATUS_RESERVED ? 'active' : 'block';
        }
        return $licenses;
    }
    private function getNewStatus($action)
    {
        return isset($this->actionStatuses[$action]) ? $this->actionStatuses[$action] : false;
    }
}
