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

class Revise {
    /** @var modX */
    protected $modx;
    protected $options = array();
    protected $drafts = array();
    protected $history = array();

    public function __construct(&$modx, array $options = array()) {
        $this->modx =& $modx;
        $corePath = $this->getOption('core_path', $options, MODX_CORE_PATH) . 'components/revise/';
        $assetsUrl = $this->getOption('assets_url', $options, MODX_ASSETS_URL) . 'components/revise/';
        $connectorUrl = $assetsUrl . 'connector.php';
        $this->options = array_merge(
            array(
                'assetsUrl' => $assetsUrl,
                'connectorUrl' => $connectorUrl,
                'corePath' => $corePath,
                'modelPath' => $corePath . 'model/',
                'processorsPath' => $corePath . 'processors/',
            ),
            $options
        );
        $this->modx->addPackage('revise', $this->getOption('core_path', null, MODX_CORE_PATH) . 'components/revise/model/');
        $this->modx->lexicon->load('revise:default');
        $this->modx->loadClass('ReviseObject');
        $this->modx->loadClass('ReviseResourceObject');
    }

    public function getOption($key, $options = null, $default = null) {
        $value = $default;
        if (is_array($options) && array_key_exists($key, $options)) {
            $value = $options[$key];
        } elseif (is_array($this->options) && array_key_exists($key, $this->options)) {
            $value = $this->options[$key];
        } elseif (is_array($this->modx->config) && array_key_exists("revise.{$key}", $this->modx->config)) {
            $value = $this->modx->config["revise.{$key}"];
        }
        return $value;
    }

    public function setOption($key, $value) {
        if (!is_string($key) || $key === '') throw new InvalidArgumentException("Attempt to set value for an invalid option key");
        $this->options[$key] = $value;
    }

    public function apply($revision) {
        return $revision->apply();
    }

    public function view($revision) {
        return $revision->view();
    }

    public function gc() {
        //TODO: implement garbage collection
    }
}
