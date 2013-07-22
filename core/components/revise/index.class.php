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

        $this->addJavascript(MODX_MANAGER_URL.'assets/modext/util/datetime.js');
        $this->addJavascript($this->revise->getOption('assetsUrl') . 'js/revise.js');

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Revise.config = '.$this->modx->toJSON($this->revise->getOptions()).';
            Revise.request = '.$this->modx->toJSON($_GET).';
        });
        </script>');
    }
}

class IndexManagerController extends ReviseManagerController {
    public static function getDefaultController() { return 'home'; }
}
