<?php
class ReviseResourceDraftCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'ReviseResourceDraft';
    /** @var ReviseResourceDraft */
    public $object;
    public $objectType = 'revise_resource_draft';
    public $languageTopics = array('resource', 'revise:default');

    public function initialize() {
        $result = true;
        $this->setDefaultProperties(
            array(
                'user' => $this->modx->getUser()->id,
                'time' => strftime("%Y-%m-%d %H:%M:%S"),
                'singleDraft' => $this->modx->revise->getOption('singleDraft', null, true),
            )
        );
        if ($this->getProperty('singleDraft', true)) {
            $this->object = $this->modx->getObject(
                $this->classKey,
                array(
                    'source' => $this->getProperty('source', 0),
                    'user' => $this->getProperty('user', 0)
                )
            );
        }
        if (!$this->getProperty('singleDraft', true) || empty($this->object)) {
            $result = parent::initialize();
        }
        return $result;
    }
}

return 'ReviseResourceDraftCreateProcessor';
