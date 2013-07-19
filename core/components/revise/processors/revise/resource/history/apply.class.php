<?php
class ReviseResourceHistoryApplyProcessor extends modObjectGetProcessor {
    public $classKey = 'ReviseResourceHistory';
    /** @var ReviseResourceHistory */
    public $object;
    public $objectType = 'revise_resource_history';
    public $languageTopics = array('resource', 'revise:default');

    public function process() {
        if (!$this->object->apply()) {
            $this->failure($this->modx->lexicon('revise.resource_history_apply_err'), $this->object->get('data'));
        }

        $this->beforeOutput();
        return $this->cleanup();
    }

    public function cleanup() {
        if ($this->getProperty('refreshCache', false)) {
            $this->modx->getCacheManager()->refresh();
        }
        return parent::cleanup();
    }
}

return 'ReviseResourceHistoryApplyProcessor';
