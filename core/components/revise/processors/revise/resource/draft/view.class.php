<?php
class ReviseResourceDraftViewProcessor extends modObjectGetProcessor {
    public $classKey = 'ReviseResourceDraft';
    /** @var ReviseResourceDraft */
    public $object;
    public $objectType = 'revise_resource_draft';
    public $languageTopics = array('resource', 'revise:default');

    public function cleanup() {
        $view = $this->object->view();
        if ($this->getProperty('renderView', true)) {
            foreach ($view['headers'] as $header) header($header);
            return $view['body'];
        }
        return $this->success('', $view);
    }
}

return 'ReviseResourceDraftViewProcessor';
