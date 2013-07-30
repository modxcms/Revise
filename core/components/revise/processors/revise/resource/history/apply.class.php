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
        $data = $this->object->toArray('', false, true, true);
        if ($this->getProperty('refreshCache', false)) {
            $context = array($data['Resource']['context_key']);
            $this->modx->getCacheManager()->refresh(
                array(
                    'auto_publish' => array('contexts' => $context),
                    'context_settings' => array('contexts' => $context),
                    'db' => array(),
                    'resource' => array('contexts' => $context),
                )
            );
        }
        return $this->success('', $data);
    }
}

return 'ReviseResourceHistoryApplyProcessor';
