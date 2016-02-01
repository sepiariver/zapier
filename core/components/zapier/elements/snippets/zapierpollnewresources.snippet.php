<?php
/**
 * zapierPollNewResources
 * 
 * Serves a polling endpoint for newly published MODX Resources.
 *  
 * @package Zapier
 * @author @sepiariver <yj@modx.com>
 * Copyright 2015 by YJ Tso
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * OPTIONS:
 * &parent -    (string) comma-separated list of parent resources. Default value polls all 
 *              resources, which can be a big query. Default ""
 * &sortby -    (string) the field on which to sort the result set. Default "publishedon"
 * &sortdir -   (string) the sort direction. Default "DESC"
 * note: zapier expects newly created objects to be listed in reverse chronological order
 * thus the default behaviour. Modifying the defaults can have unexpected results.
 *
 **/
$parents = array();
$parents = array_filter(array_map('trim', explode(',', $modx->getOption('parent', $scriptProperties, ''))));
$sortby = $modx->getOption('sortby', $scriptProperties, 'publishedon');
$sortdir = $modx->getOption('sortdir', $scriptProperties, 'DESC');

$c = $modx->newQuery('modResource');
$c->where(array(
    
    'published' => 1,
    'searchable' => 1,
    'deleted' => 0,
    
    ));
if (!empty($parents)) {
    $c->where(array(
       'parent:IN' => $parents, 
    ));
}
$c->sortby($sortby, $sortdir);
$resources = $modx->getCollection('modResource', $c);

$idx = 0;
$output = array();
foreach ($resources as $res) {
    $fields = array();
    $fields = $res->toArray('',true,true);
    foreach ($fields as $key => $val) {
        $fields[$key] = str_replace(array('[',']'), array('&#91;','&#92;'), $val);
    }
    $output[$idx] = $fields;
    $idx++;
}    
return $modx->toJSON($output);