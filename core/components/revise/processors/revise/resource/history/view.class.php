<?php
class ReviseResourceHistoryViewProcessor extends modObjectGetProcessor {
    public $classKey = 'ReviseResourceHistory';
    /** @var ReviseResourceHistory */
    public $object;
    public $objectType = 'revise_resource_history';
    public $languageTopics = array('resource', 'revise:default');

    public function cleanup() {
        return $this->success(
            '',
            $this->object->view()
        );
    }
}

return 'ReviseResourceHistoryViewProcessor';
