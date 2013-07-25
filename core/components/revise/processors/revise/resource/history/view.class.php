<?php
class ReviseResourceHistoryViewProcessor extends modObjectGetProcessor {
    public $classKey = 'ReviseResourceHistory';
    /** @var ReviseResourceHistory */
    public $object;
    public $objectType = 'revise_resource_history';
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

return 'ReviseResourceHistoryViewProcessor';
