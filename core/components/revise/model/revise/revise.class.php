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

/**
 * The Revise service class.
 *
 * Use modX::getService() to load this class and the Revise model classes.
 */
class Revise {
    /** @var modX */
    protected $modx;
    protected $drafts = array();
    protected $history = array();
    public $options = array();

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
        $this->modx->addPackage('revise', $this->getOption('modelPath'));
        $this->modx->lexicon->load('revise:default');
        $this->modx->loadClass('ReviseObject');
        $this->modx->loadClass('ReviseResourceObject');
    }

    /**
     * Get local options configured for this instance.
     *
     * @return array An array of local config options.
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Get the value of a config option.
     *
     * NOTE: If the option is not found locally, the key is prepended with
     * 'revise.' before searching again, both locally and then in modX settings.
     *
     * @param string $key The option identifier.
     * @param array|null $options An optional array of option overrides.
     * @param mixed $default The default value to use if no option is found.
     * @param bool $skipEmpty Determine if empty string values should be skipped.
     *
     * @return mixed The value of the config option.
     */
    public function getOption($key, $options = null, $default = null, $skipEmpty = false) {
        if (is_array($options) && array_key_exists($key, $options) && (!$skipEmpty || ($skipEmpty && $options[$key] !== ''))) {
            $value = $options[$key];
        } elseif (is_array($this->options) && array_key_exists($key, $this->options) && (!$skipEmpty || ($skipEmpty && $this->options[$key] !== ''))) {
            $value = $this->options[$key];
        } else {
            $value = $this->modx->getOption("revise.{$key}", $options, $default, $skipEmpty);
        }
        return $value;
    }

    /**
     * Set a config option for this instance.
     *
     * @param string $key The option identifier.
     * @param mixed $value The value to assign to the option.
     *
     * @throws InvalidArgumentException If the option key is not a valid string identifier.
     */
    public function setOption($key, $value) {
        if (!is_string($key) || $key === '') throw new InvalidArgumentException("Attempt to set value for an invalid option key");
        $this->options[$key] = $value;
    }

    /**
     * Apply data from a ReviseObject to it's source.
     *
     * @param ReviseObject $revision The revision to apply.
     *
     * @return bool true if successful; otherwise false.
     */
    public function apply($revision) {
        return $revision->apply();
    }

    /**
     * View the ReviseObject data as appropriate for it's source.
     *
     * @param ReviseObject $revision The revision to view.
     *
     * @return mixed Various implementations will return different types of data.
     */
    public function view($revision) {
        return $revision->view();
    }

    /**
     * Perform garbage collection on all ReviseObject derivatives.
     *
     * @return array An array of classes processed with a count of records
     * removed by the process for each class.
     */
    public function gc() {
        $removed = array();
        //TODO: implement global garbage collection
        return $removed;
    }
}
