<?php
/**
 * zapierPollSavedForms
 * 
 * Serves a polling endpoint for saved FormIt forms.
 * REQUIRES the FormIt Extra! Call this as a FormIt hook.
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
 **/

if (!$hook) return false;

$zapierSavedFormsKey = $modx->getOption('zapierSavedFormsKey', $scriptProperties, 'saved_forms');

// Get forms
$forms = $modx->getCollection('FormItForm');

// Output
$output = array();
$idx = 1;
foreach ($forms as $form) {
    $values = $form->toArray(); // get the form record
    $formFields = $modx->fromJSON($values['values']); // get form fields as array
    $formFields = array_diff_key($formFields, $values); // don't override form record keys
    $values = array_merge($values, $formFields); // make values top-level for easy access
    unset($values['values']); // destroy nested array of field values
    $output[$idx] = $values;
    $idx++;
}
$output = array_reverse($output);
$hook->setValue($zapierSavedFormsKey, $modx->toJSON($output));
return true;