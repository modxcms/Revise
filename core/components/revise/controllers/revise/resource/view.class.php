<?php
class ReviseResourceViewManagerController extends modExtraManagerController {
    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false;

    private $object;
    private $objectType;
    private $classKey;

    public function initialize() {
        $corePath = $this->modx->getOption('revise.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH));
        $this->modx->getService(
            'revise',
            'Revise',
            $corePath . 'components/revise/model/revise/',
            array(
                'core_path' => $corePath
            )
        );
    }

    public function process(array $scriptProperties = array()) {
        $this->objectType = isset($scriptProperties['type']) ? $scriptProperties['type'] : 'revise_resource_draft';
        $id = isset($scriptProperties['id']) ? (integer)$scriptProperties['id'] : 0;
        $split = explode('_', $this->objectType);
        array_walk($split, 'ucfirst');
        $this->classKey = implode('', $split);
        if (!$this->modx->loadClass($this->classKey)) {
            return $this->modx->error->failure($this->modx->lexicon('revise_object_class_nf', array('classKey' => $this->classKey)));
        }
        if ($id < 1) {
            return $this->modx->error->failure($this->modx->lexicon($this->objectType . '_err_ns'));
        }

        $this->object = $this->modx->getObject($this->classKey, $id);
        if (empty($this->object)) $this->modx->error->failure($this->modx->lexicon($this->objectType . '_err_nfs'));

        $response = $this->object->view();
        if (isset($response['headers']) && is_array($response['headers'])) {
            foreach ($response['headers'] as $header) header($header);
        }
        if (!isset($response['body'])) {
            return $this->modx->error->failure(
                $this->modx->lexicon($this->objectType . '_err_view'),
                array('id' => $id, 'resource' => $this->object->get('source'))
            );
        }
        return $response['body'];
    }
}
