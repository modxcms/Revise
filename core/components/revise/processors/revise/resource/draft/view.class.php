<?php
class ReviseResourceDraftViewProcessor extends modObjectGetProcessor {
    public $classKey = 'ReviseResourceDraft';
    /** @var ReviseResourceDraft */
    public $object;
    public $objectType = 'revise_resource_draft';
    public $languageTopics = array('resource', 'revise:default');

    public function cleanup() {
        $view = $this->object->view(array('ignoreTemplate' => $this->getProperty('ignoreTemplate', false)));
        if ($this->getProperty('renderView', true)) {
            if ($this->getProperty('setHeaders', false) && !$this->getProperty('ignoreTemplate', false)) {
                foreach ($view['headers'] as $header) header($header);
            }
            return $view['body'];
        }
        return $this->success('', $view);
    }
}

return 'ReviseResourceDraftViewProcessor';
