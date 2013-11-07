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
          /** @var modResource $existingResource */
          $existingResource = $modx->getObject('modResource', $id, false);


		  /** @var modProcessorResponse $response */
		  $response = $modx->runProcessor(
			'revise/resource/history/create',
			array(
			  'source' => $existingResource->get('id'),
			  'data' => $existingResource->toArray('', true, true, false)
			),
			array('processors_path' => $revise->getOption('processorsPath'))
		  );
		  
		  if ($response->isError()) {
			$modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage(), '', 'modPlugin::Revise', __FILE__, __LINE__);
		  }

        }

        break;


    case "OnDocFormRender":
        /* Implement rendering of ReviseResourceDraft creation/preview controls */


        /* Preaper data for Ajax request */
        $existingResource = $modx->getObject('modResource', $id, false);

        /*Get the last available draft and store it.*/
		$lastReviseId = 0;
		if (is_object($existingResource)) {
          $result = $modx->query("SELECT id FROM modx_revise_resource_drafts WHERE source=$id ORDER BY time DESC");
		  if (is_object($result)) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
			$lastReviseId = $row['id'];
		  }		  

		}
        /* Init Params for use in EXT below */
		$reviseParams = array(
		  'siteurl' => $modx->getOption('site_url'),
		  'id' => is_object($existingResource) ? $existingResource->get('id') : 0,
		  'data' => is_object($existingResource) ? $modx->toJSON($existingResource->toArray('', true, true, false)) : array(),
		  'lastReviseId' => $lastReviseId ? $lastReviseId : 0
		 );

		$data = '<script> var reviseParams = ' . $modx->toJSON($reviseParams) . ';</script>';



		// TO DO disable save draft if resource locked \ or if no permission

		/* Render buttons\checkbox on Form Render */

$data .= <<<HTML
<script> 

Ext.onReady(function(){
  Ext.defer(function(){

               	var panel = Ext.getCmp('modx-resource-main-right');
                var buttons = Ext.getCmp('modx-action-buttons');

                //Add Toolbar Spacer 
				buttons.add({
					 xtype: 'tbspacer',
					 width: 8
				});							

                //Add Savedraft Button
				buttons.add({
					 id: 'savedraft',
					 name: 'savedraft',
					 xtype: 'button',
					 cls: 'x-btn x-btn-text bmenu x-btn-noicon',
					 text: 'Save Draft',
					 disabled : false,
					 handler: function () {

								//Save content to "ta" for RTE (eg. codemirror,tinyMCE)
                                if (typeof MODx.editor != 'undefined') { MODx.editor.save(); }

                                formData = Ext.getCmp('modx-panel-resource').getForm().getValues();

								//Override content with ta
								formData.content = formData.ta; 

								MODx.Ajax.request({
										 url: reviseParams.siteurl + 'assets/components/revise/connector.php',
										 params: {
										    action: 'revise/resource/draft/create',
											source: reviseParams.id,
											data : Ext.encode(formData),
											singleDraft : (Ext.getCmp('savedraftcheck').getValue() ? 1 : 0)
										 },
										 listeners: {
										    success:{fn:function(result) {
										       
											   MODx.msg.status({
														title: _('save_successful'),
														message: result['success'] ? (result['message'] ? result['message'] : _('success')) : _('error'),
														delay: 3
										       });
                                                  reviseParams.lastReviseId = result.object.id;
												  Ext.getCmp('previewdraft').setDisabled(false);

										       },scope:this}, 


											failure: {fn:function(response, opts) {
											   MODx.msg.status({
														title: _('failure'),
														message: 'server-side failure with status code ' + response.status,
														delay: 3
										       });
											},scope:this}

									    }
								});

							  } 
				});

                //Add Toolbar Spacer
				buttons.add({
					 xtype: 'tbspacer',
					 width: 5
				});							

                //Add Previewdraft button to the panel
				buttons.add({
					 id: 'previewdraft',
					 name: 'previewdraft',
					 xtype: 'button',
					 cls: 'x-btn x-btn-text bmenu x-btn-noicon',
					 text: 'Preview Draft',
					 disabled : (reviseParams.lastReviseId === 0),
                     handler : function(){
                       if (reviseParams.lastReviseId > 0){
					     var url = reviseParams.siteurl + 'assets/components/revise/connector.php' + '?action=revise/resource/draft/view&id=' + reviseParams.lastReviseId + '&HTTP_MODAUTH=' + MODx.siteId;
                         window.open(url);
					   } else {
                         alert('No drafts for this resource found.');
					   }
					 }
				 });

                //Add Single Draft checkbox to the panel
				panel.add({
                   id: 'savedraftcheck',
                   name: 'savedraftcheck',
                   hideLabel: true,
                   xtype: 'xcheckbox',
                   boxLabel: 'Single Draft',
				   disabled : false
                });

                //Refresh layout
                buttons.doLayout();
				panel.doLayout();


  }, 1000);
});

</script>
HTML;

$modx->controller->addHtml($data);

                  break;
}


return true;
