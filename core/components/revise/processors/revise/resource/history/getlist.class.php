<?php
class ReviseResourceHistoryGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'ReviseResourceHistory';
    /** @var ReviseResourceHistory */
    public $object;
    public $objectType = 'revise_resource_history';
    public $languageTopics = array('resource', 'revise:default');
    public $defaultSortField = 'time';
    public $defaultSortDirection = 'DESC';

    public function getData() {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey,$c);
        $c = $this->prepareQueryAfterCount($c);

        $c->leftJoin('modUser', 'User', array("User.id = {$this->classKey}.user"));
        $c->leftJoin('modResource', 'Resource', array("Resource.id = {$this->classKey}.source"));

        $select = array(
            $this->modx->getSelectColumns($this->classKey, $this->classKey, ''),
            $this->modx->getSelectColumns('modUser', 'User', '', array('username')),
            $this->modx->getSelectColumns('modResource', 'Resource', '', array('pagetitle'))
        );
        $c->select(implode(', ', $select));

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey,$this->getProperty('sortAlias',$sortClassKey),'',array($this->getProperty('sort')));
        if (empty($sortKey)) $sortKey = $this->getProperty('sort');
        $c->sortby($sortKey,$this->getProperty('dir'));
        if (!in_array($this->getProperty('sort'), array('id'))) {
            $c->sortby('id', $this->getProperty('dir'));
        }
        if ($limit > 0) {
            $c->limit($limit,$start);
        }

        $data['results'] = $this->modx->getCollection($this->classKey, $c);
        return $data;
    }

    public function afterIteration(array $list) {
        foreach ($list as &$item) {
            $item['menu'] = array(
                array(
                    'text' => $this->modx->lexicon('revise_view_resource_history'),
                    'handler' => 'this.viewRevision'
                ),
                array(
                    'text' => $this->modx->lexicon('revise_apply_resource_history'),
                    'handler' => 'this.applyRevision'
                )
            );
        }
        return $list;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $criteria = array();
        $source = (integer)$this->getProperty('source', 0);
        if ($source > 0) {
            $criteria['source'] = $source;
        }

        $user = (integer)$this->getProperty('user', 0);
        if ($user > 0) {
            $criteria['user'] = $user;
        }

        $start = $this->getProperty('after', 0);
        if (!empty($start)) {
            $criteria['time:>'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($start));
        }
        $end = $this->getProperty('before', 0);
        if (!empty($end)) {
            $criteria['time:<'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($end));
        }

        if (!empty($criteria)) {
            $c->where($criteria);
        }
        return $c;
    }
}

return 'ReviseResourceHistoryGetListProcessor';
