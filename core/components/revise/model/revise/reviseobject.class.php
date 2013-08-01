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

abstract class ReviseObject extends xPDOSimpleObject {
    /** @var xPDO|modX */
    public $xpdo = null;

    /**
     * Apply this Revision back to it's source.
     *
     * @return bool true if successfully applied, otherwise false.
     */
    abstract public function apply();

    /**
     * View this Revision as appropriate based on it's source.
     *
     * @param array $options An array of options for the view.
     *
     * @return mixed A response appropriate for the source and view options.
     */
    abstract public function view(array $options = array());

    public function save($cacheFlag = null) {
        if (isset($this->xpdo->revise) && !$this->xpdo->getOption(xPDO::OPT_SETUP, null, false)) {
            $gc = $this->gc();
            if ($gc !== false) {
                $this->xpdo->log(xPDO::LOG_LEVEL_INFO, "Revise GC removed {$gc} instances of {$this->_class}", '', __METHOD__, __FILE__, __LINE__);
            } else {
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "Revise GC encountered an error", '', __METHOD__, __FILE__, __LINE__);
            }
        }
        return parent::save($cacheFlag);
    }

    public function remove(array $ancestors = array()) {
        $return = parent::remove($ancestors);
        if (isset($this->xpdo->revise) && !$this->xpdo->getOption(xPDO::OPT_SETUP, null, false)) {
            $gc = $this->gc();
            if ($gc !== false) {
                $this->xpdo->log(xPDO::LOG_LEVEL_INFO, "Revise GC removed {$gc} instances of {$this->_class}", '', __METHOD__, __FILE__, __LINE__);
            } else {
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "Revise GC encountered an error", '', __METHOD__, __FILE__, __LINE__);
            }
        }
        return $return;
    }

    /**
     * Perform Garbage Collection, removing objects older than gc_maxlifetime.
     *
     * @return int|bool The number of objects removed or false on failure.
     */
    public function gc() {
        $removed = 0;
        $maxLifetime = (integer)$this->xpdo->revise->getOption('gc_maxlifetime', null, 0);
        if ($maxLifetime > 0) {
            $inSeconds = $maxLifetime * 60 * 60 * 24;
            $removed = $this->xpdo->removeCollection($this->_class, array('time:<' => strftime("%Y-%m-%d %H:%M:%S", time() - $inSeconds)));
        }
        return $removed;
    }
}
