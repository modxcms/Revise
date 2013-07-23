<?php
class ReviseResourceHistoryViewProcessor extends modObjectGetProcessor {
    public $classKey = 'ReviseResourceHistory';
    /** @var ReviseResourceHistory */
    public $object;
    public $objectType = 'revise_resource_history';
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

return 'ReviseResourceHistoryViewProcessor';
