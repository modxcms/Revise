<?php
class ReviseResourceHistoryCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'ReviseResourceHistory';

    public function initialize() {
        $this->setDefaultProperties(
            array(
                'user' => $this->modx->getUser()->id,
                'time' => strftime("%Y-%m-%d %H:%M:%S"),
            )
        );
        return parent::initialize();
    }
}

return 'ReviseResourceHistoryCreateProcessor';
