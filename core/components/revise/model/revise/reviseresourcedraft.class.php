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

class ReviseResourceDraft extends ReviseResourceObject {
    public function apply() {
        $applied = false;
        if (!$this->getOne('Resource')) {
            $this->Resource = $this->xpdo->newObject('modResource');
        }
        $this->prepareResource();
        if ($this->createResourceHistory()) {
            $applied = $this->Resource->save();
        }
        return $applied;
    }

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
}
