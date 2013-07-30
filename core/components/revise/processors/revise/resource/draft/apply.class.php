<?php
class ReviseResourceDraftApplyProcessor extends modObjectGetProcessor {
    public $classKey = 'ReviseResourceDraft';
    /** @var ReviseResourceDraft */
    public $object;
    public $objectType = 'revise_resource_draft';
    public $languageTopics = array('resource', 'revise:default');

    public function process() {
        if (!$this->object->apply()) {
            $this->failure($this->modx->lexicon('revise_resource_draft_apply_err'), $this->object->get('data'));
        }

        $this->beforeOutput();
        return $this->cleanup();
    }

    public function cleanup() {
        $data = $this->object->toArray('', false, true, true);
        if ($this->getProperty('refreshCache', false)) {
            $this->modx->getCacheManager()->refresh();
        }
        if ($this->getProperty('removeDraft', true)) {
            $this->object->remove();
        }
        return $this->success('', $data);
//        return parent::cleanup();
    }
}

return 'ReviseResourceDraftApplyProcessor';
