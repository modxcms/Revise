<?php
/**
 * @var modX $modx
 * @var modResource $resource
 * @var string $mode
 */
$corePath = $modx->getOption('revise.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH));
switch ($modx->event->name) {
    case "OnBeforeDocFormSave":
        /* Create a ReviseResourceHistory record when updating a Resource */
        if (empty($reloadOnly) && !empty($resource) && isset($mode) && $mode === modSystemEvent::MODE_UPD) {
            $revise = $modx->getService('revise', 'Revise', $corePath . 'components/revise/model/revise/', array('core_path' => $corePath));

            /* get the resource fresh without the pending changes */
            $existingResource = $modx->getObject('modResource', $id, false);

            /** @var modProcessorResponse $response */
            $response = $modx->runProcessor(
                'revise/resource/history/create',
                array(
                    'source' => $existingResource->get('id'),
                    'data' => $existingResource->toArray('', true, true, false)
                ),
                array('processors_path' => $revise->getOption('core_path') . 'components/revise/processors/')
            );
            if ($response->isError()) {
                $modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage(), '', 'modPlugin::Revise', __FILE__, __LINE__);
            }
        }
        break;

    case "OnDocFormPrerender":
        /* TODO: implement rendering of ReviseResourceDraft creation/preview controls */
        break;
}
return true;
