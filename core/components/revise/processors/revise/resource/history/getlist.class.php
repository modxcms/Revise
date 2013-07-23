<?php
class ReviseResourceHistoryGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'ReviseResourceHistory';
    /** @var ReviseResourceHistory */
    public $object;
    public $objectType = 'revise_resource_history';
    public $languageTopics = array('resource', 'revise:default');
    public $defaultSortField = 'time';

    public function getData() {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey,$c);
        $c = $this->prepareQueryAfterCount($c);

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

    public function afterIteration($list) {
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
        $resource = (integer)$this->getProperty('resource', 0);
        if ($resource > 0) {
            $criteria['resource'] = $resource;
        }

        $user = (integer)$this->getProperty('user', 0);
        if ($user > 0) {
            $criteria['user'] = $user;
        }

        $start = (integer)$this->getProperty('startTime', 0);
        if ($start > 0) {
            $criteria['time:>'] = strftime("%Y-%m-%d %H:%M:%S", $start);
        }
        $end = (integer)$this->getProperty('endTime', 0);
        if ($end > 0) {
            $criteria['time:<'] = strftime("%Y-%m-%d %H:%M:%S", $end);
        }

        if (!empty($criteria)) {
            $c->where($criteria);
        }
        return $c;
    }
}

return 'ReviseResourceHistoryGetListProcessor';
