<?php
/*
 * Revise
 *
 * Copyright 2013 by Jason Coward <jason@modx.com>
 *
 * This file is part of Revise, a simple versioning component for MODX Revolution.
 *
 * Revise is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Revise is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Revise; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 */

abstract class ReviseResourceObject extends ReviseObject {
    public function view(array $options = array()) {
        if (!$this->getOne('Resource')) {
            $this->Resource = $this->xpdo->newObject('modResource');
        }
        $this->prepareResource();
        $ignoreTemplate = isset($options['ignoreTemplate']) && !empty($options['ignoreTemplate']);
        return $this->prepareOutput($ignoreTemplate);
    }

    public function apply() {
        $create = false;
        $applied = false;
        if (!$this->getOne('Resource')) {
            $this->Resource = $this->xpdo->newObject('modResource');
            $create = true;
        }
        $this->prepareResource();
        if ($create || $this->createResourceHistory()) {
            /** @var modProcessorResponse $response */
            $response = $this->xpdo->runProcessor(
                'resource/' . ($create ? 'create' : 'update'),
                $this->Resource->toArray()
            );
            
            if (!$response->isError()) {
                $applied = true;
            } else {
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, $response->getMessage(), '', __METHOD__, __FILE__, __LINE__);
            }
        }
        return $applied;
    }

    /**
     * Create a ReviseResourceHistory object
     *
     * @return int|bool The object id of the object or false on failure.
     */
    protected function createResourceHistory() {
        $created = false;

        /** @var modProcessorResponse $response */
        $response = $this->xpdo->runProcessor(
            'revise/resource/history/create',
            array(
                'source' => $this->Resource->get('id'),
                'data' => $this->Resource->toArray('', true, true, false)
            ),
            array('processors_path' => $this->xpdo->revise->getOption('processorsPath'))
        );

        if (!$response->isError()) {
            $object = $response->getObject();
            $created = $object['id'];
        } else {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, $response->getMessage(), '', __METHOD__, __FILE__, __LINE__);
        }
        return $created;
    }

    /**
     * Prepare the Resource from the revision data.
     */
    protected function prepareResource() {
        $this->Resource->fromArray($this->get('data'), '', $this->Resource->isNew(), true, true);
        //TODO: handle temporary tv_data
//        $tvs = array();
//        foreach ($this->get('tv_data') as $tvData) {
//            /** @var modTemplateVar $tv */
//            $tv = $this->xpdo->newObject('modTemplateVar');
//            $tv->fromArray($tvData, '', false, true, true);
//            $tvs[] = $tv;
//        }
//        $this->Resource->addMany($tvs);
    }

    /**
     * Prepare the output to view the Resource revision.
     *
     * @param bool $ignoreTemplate Indicates if the Resource Template should be ignored.
     *
     * @return array An associated array containing an array of output headers and a string body.
     */
    protected function prepareOutput($ignoreTemplate = false) {
        $output = array();

        $maxIterations = (integer) $this->getOption('max_iterations', null, 10);
        $currentResource = $this->xpdo->resource;
        $currentResourceIdentifier = $this->xpdo->resourceIdentifier;
        $currentElementCache = $this->xpdo->elementCache;
        $currentJscripts= $this->xpdo->jscripts;
        $currentSjscripts= $this->xpdo->sjscripts;
        $currentLoadedjscripts= $this->xpdo->loadedjscripts;

        $currentContext = $this->xpdo->context->get('key');
        if ($this->Resource->get('context_key') !== $currentContext) {
            $this->xpdo->switchContext($this->Resource->get('context_key'));
        }

        $this->xpdo->resource =& $this->Resource;
        $this->xpdo->resourceIdentifier = $this->Resource->get('id');
        $this->xpdo->elementCache = array();

        $this->Resource->getOne('ContentType');

        ob_start();

        if (!$this->Resource->ContentType->get('binary')) {
            if ($ignoreTemplate) {
                $this->Resource->_output = $this->Resource->getContent();
                $this->xpdo->getParser()->processElementTags('[[*content]]', $this->Resource->_output, false, false, '[[', ']]', array(), $maxIterations);
            } else {
                $this->Resource->_output = $this->Resource->process();
            }
            $this->Resource->_jscripts= $this->xpdo->jscripts;
            $this->Resource->_sjscripts= $this->xpdo->sjscripts;
            $this->Resource->_loadedjscripts= $this->xpdo->loadedjscripts;

            $this->xpdo->getParser()->processElementTags('', $this->Resource->_output, true, false, '[[', ']]', array(), $maxIterations);
            $this->xpdo->getParser()->processElementTags('', $this->Resource->_output, true, true, '[[', ']]', array(), $maxIterations);

            if (strpos($this->Resource->ContentType->get('mime_type'), 'text/html') !== false) {
                /* Insert Startup jscripts & CSS scripts into template - template must have a </head> tag */
                if (($js= $this->xpdo->getRegisteredClientStartupScripts()) && (strpos($this->Resource->_output, '</head>') !== false)) {
                    /* change to just before closing </head> */
                    $this->Resource->_output= preg_replace("/(<\/head>)/i", $js . "\n\\1", $this->Resource->_output,1);
                }

                /* Insert jscripts & html block into template - template must have a </body> tag */
                if ((strpos($this->Resource->_output, '</body>') !== false) && ($js= $this->xpdo->getRegisteredClientScripts())) {
                    $this->Resource->_output= preg_replace("/(<\/body>)/i", $js . "\n\\1", $this->Resource->_output,1);
                }
            }

            if ($this->getOption('view_events', null, false)) {
                $this->xpdo->invokeEvent('OnWebPagePrerender');
            }

            $totalTime= (microtime(true) - $this->xpdo->startTime);
            $queryTime= $this->xpdo->queryTime;
            $queryTime= sprintf("%2.4f s", $queryTime);
            $queries= isset ($this->xpdo->executedQueries) ? $this->xpdo->executedQueries : 0;
            $totalTime= sprintf("%2.4f s", $totalTime);
            $phpTime= $totalTime - $queryTime;
            $phpTime= sprintf("%2.4f s", $phpTime);
            $source= "revise";
            $this->Resource->_output= str_replace("[^q^]", $queries, $this->Resource->_output);
            $this->Resource->_output= str_replace("[^qt^]", $queryTime, $this->Resource->_output);
            $this->Resource->_output= str_replace("[^p^]", $phpTime, $this->Resource->_output);
            $this->Resource->_output= str_replace("[^t^]", $totalTime, $this->Resource->_output);
            $this->Resource->_output= str_replace("[^s^]", $source, $this->Resource->_output);
        } else {
            if ($this->getOption('view_events', null, false)) {
                $this->xpdo->invokeEvent('OnWebPagePrerender');
            }
        }

        /* send out content-type, content-disposition, and custom headers from the content type */
        if ($this->xpdo->getOption('set_header')) {
            $type= $this->Resource->ContentType->get('mime_type') ? $this->Resource->ContentType->get('mime_type') : 'text/html';
            $header= 'Content-Type: ' . $type;
            if (!$this->Resource->ContentType->get('binary')) {
                $charset= $this->xpdo->getOption('modx_charset',null,'UTF-8');
                $header .= '; charset=' . $charset;
            }
            $output['headers'][] = $header;
            $dispositionSet= false;
            if ($customHeaders= $this->Resource->ContentType->get('headers')) {
                foreach ($customHeaders as $headerKey => $headerString) {
                    $output['headers'][] = $headerString;
                    if (strpos($headerString, 'Content-Disposition:') !== false) $dispositionSet= true;
                }
            }
            if (!$dispositionSet && $this->Resource->get('content_dispo')) {
                if ($alias= $this->Resource->get('uri')) {
                    $name= basename($alias);
                } elseif ($this->Resource->get('alias')) {
                    $name= $this->Resource->get('alias');
                    if ($ext= $this->Resource->ContentType->getExtension()) {
                        $name .= ".{$ext}";
                    }
                } elseif ($name= $this->Resource->get('pagetitle')) {
                    $name= $this->Resource->cleanAlias($name);
                    if ($ext= $this->Resource->ContentType->getExtension()) {
                        $name .= ".{$ext}";
                    }
                } else {
                    $name= 'download';
                    if ($ext= $this->Resource->ContentType->getExtension()) {
                        $name .= ".{$ext}";
                    }
                }
                $output['headers'][] = 'Cache-Control: public';
                $output['headers'][] = 'Content-Disposition: attachment; filename=' . $name;
                $output['headers'][] =  'Vary: User-Agent';
            }
        }

        if ($this->Resource instanceof modStaticResource && $this->Resource->ContentType->get('binary')) {
            $this->Resource->process();
        } else {
            if ($this->Resource->ContentType->get('binary')) {
                $this->Resource->_output = $this->Resource->process();
            }
            $output['body'] = $this->Resource->_output;
        }

        ob_end_flush();

        if ($currentContext !== $this->xpdo->context->get('key')) {
            $this->xpdo->switchContext($currentContext);
        }
        $this->xpdo->resource = $currentResource;
        $this->xpdo->resourceIdentifier = $currentResourceIdentifier;
        $this->xpdo->elementCache = $currentElementCache;
        $this->xpdo->jscripts = $currentJscripts;
        $this->xpdo->sjscripts = $currentSjscripts;
        $this->xpdo->loadedjscripts = $currentLoadedjscripts;

        return $output;
    }
}
