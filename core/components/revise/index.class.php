<?php
abstract class ReviseManagerController extends modExtraManagerController {
    /** @var Revise */
    public $revise;

    public function initialize() {
        $corePath = $this->modx->getOption('revise.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH));
        $this->revise = $this->modx->getService(
            'revise',
            'Revise',
            $corePath . 'components/revise/model/revise/',
            array(
                'core_path' => $corePath
            )
        );
    }
}

class IndexManagerController extends ReviseManagerController {
    public static function getDefaultController() { return 'home'; }
}
