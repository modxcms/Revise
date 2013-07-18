<?php
class ReviseResourceDraftCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'ReviseResourceDraft';

    public function initialize() {
        $result = true;
        $this->setDefaultProperties(
            array(
                'user' => $this->modx->getUser()->id,
                'time' => strftime("%Y-%m-%d %H:%M:%S"),
                'single_draft' => $this->modx->revise->getOption('single_draft', null, true),
            )
        );
        if ($this->getProperty('single_draft', true)) {
            $this->object = $this->modx->getObject(
                $this->classKey,
                array(
                    'source' => $this->getProperty('source', 0),
                    'user' => $this->getProperty('user', 0)
                )
            );
        }
        if (!$this->getProperty('single_draft', true) || empty($this->object)) {
            $result = parent::initialize();
        }
        return $result;
    }
}

return 'ReviseResourceDraftCreateProcessor';
