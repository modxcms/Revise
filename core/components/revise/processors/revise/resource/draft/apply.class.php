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
        if ($this->getProperty('removeDraft', true)) {
            $this->object->remove();
        }
        return $this->success('', $data);
    }
}

return 'ReviseResourceDraftApplyProcessor';
