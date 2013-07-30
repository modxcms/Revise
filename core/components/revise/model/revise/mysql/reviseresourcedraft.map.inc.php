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
$xpdo_meta_map['ReviseResourceDraft']= array (
  'package' => 'revise',
  'version' => '1.1',
  'table' => 'revise_resource_drafts',
  'extends' => 'ReviseResourceObject',
  'fields' => 
  array (
    'source' => 0,
  ),
  'fieldMeta' => 
  array (
    'source' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'fk',
    ),
  ),
);
