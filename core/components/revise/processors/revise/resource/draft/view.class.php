<?php
class ReviseResourceDraftViewProcessor extends modObjectGetProcessor {
    public $classKey = 'ReviseResourceDraft';
    /** @var ReviseResourceDraft */
    public $object;
    public $objectType = 'revise_resource_draft';
    public $languageTopics = array('resource', 'revise:default');

    public function cleanup() {
        return $this->success(
            '',
            $this->object->view()
        );
    }
}

return 'ReviseResourceDraftViewProcessor';
