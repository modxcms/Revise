<?php
class ReviseHomeManagerController extends ReviseManagerController {
    public function getPageTitle() {
        return $this->modx->lexicon('revise');
    }

    public function loadCustomCssJs() {
        $this->addJavascript($this->revise->getOption('assetsUrl') . 'js/revise.js');
        $this->addJavascript($this->revise->getOption('assetsUrl') . 'js/widgets/resource/history.grid.js');
        $this->addJavascript($this->revise->getOption('assetsUrl') . 'js/widgets/resource/drafts.grid.js');
        $this->addJavascript($this->revise->getOption('assetsUrl') . 'js/widgets/history.panel.js');
        $this->addLastJavascript($this->revise->getOption('assetsUrl') . 'js/sections/home.js');
    }

    public function getTemplateFile() {
        return $this->revise->getOption(
            'templatesPath',
            null,
            $this->revise->getOption('corePath') . 'templates/') . 'home.tpl';
    }
}
