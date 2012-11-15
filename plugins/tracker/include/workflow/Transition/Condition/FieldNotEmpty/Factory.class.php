<?php
/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'Dao.class.php';
require_once TRACKER_BASE_DIR .'/workflow/Transition.class.php';
require_once TRACKER_BASE_DIR .'/Tracker/FormElement/Tracker_FormElementFactory.class.php';

class Workflow_Transition_Condition_FieldNotEmpty_Factory {

    private $dao;

    public function __construct(Workflow_Transition_Condition_FieldNotEmpty_Dao $dao) {
        $this->dao = $dao;
    }



    public function getFieldNotEmpty(Transition $transition){
        $field = new Workflow_Transition_Condition_FieldNotEmpty($transition, $this->dao);

        $condition = $this->dao->searchByTransitionId($transition->getId());

        if($condition){
            $row = $condition->getRow();
            $field->setFieldId($row['field_id']) ;
        }

        return $field;
    }

    public function getInstanceFromXML($xml, &$xmlMapping, Transition $transition) {

        $field_not_empty = null;
        if (isset($xml->field)) {
            $xml_field            = $xml->field;
            $xml_field_attributes = $xml_field->attributes();
            $field_id             = $xmlMapping[(string)$xml_field_attributes['REF']]->getId();

            $field_not_empty = new Workflow_Transition_Condition_FieldNotEmpty($transition, $this->dao);
            $field_not_empty->setFieldId($field_id);
        }
        return $field_not_empty;
    }
}
?>