<?php
class ReviseResourceHistoryApplyProcessor extends modObjectGetProcessor {
    public $classKey = 'ReviseResourceHistory';
    public $objectType = 'revise_resource_history';
    public $languageTopics = array('resource', 'revise:default');

    public function process() {
        $data = $this->object->get('data');
        $data['editedby'] = $this->object->get('user');
        $data['editedon'] = strftime("%Y-%m-%d %H:%M:%S");

        $resource = $this->modx->getObject('modResource', $this->object->get('source'), false);
        $resource->fromArray($data, '', false, true);
        if (!$resource->save()) {
            $this->failure($this->modx->lexicon('revise.resource_history_apply_err'), $data);
        }

        $this->beforeOutput();
        return $this->cleanup();
    }
}

return 'ReviseResourceHistoryApplyProcessor';
