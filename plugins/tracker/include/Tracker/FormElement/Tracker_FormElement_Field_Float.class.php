<?php
/**
 * Copyright (c) Xerox Corporation, Codendi Team, 2001-2009. All rights reserved
 *
 * This file is a part of Codendi.
 *
 * Codendi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Codendi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codendi. If not, see <http://www.gnu.org/licenses/>.
 */

use Tuleap\Tracker\Report\Query\Advanced\FromWhere;

class Tracker_FormElement_Field_Float extends Tracker_FormElement_Field_Numeric {

    const FLOAT_DECIMALS = 4;

    public function getCriteriaFrom($criteria)
    {
        //Only filter query if field is used
        if($this->isUsed()) {
            //Only filter query if criteria is valuated
            $criteria_value = $this->getCriteriaValue($criteria);

            if ($criteria_value !== '') {
                $a = 'A_'. $this->id;
                $b = 'B_'. $this->id;
                return " INNER JOIN tracker_changeset_value AS $a
                         ON ($a.changeset_id = c.id AND $a.field_id = $this->id )
                         INNER JOIN tracker_changeset_value_float AS $b
                         ON ($b.changeset_value_id = $a.id
                             AND ". $this->buildMatchExpression("$b.value", $criteria_value) ."
                         ) ";
            }
        }
        return '';
    }

    public function fetchCriteriaValue($criteria)
    {
        $html           = '<input type="text" name="criteria['. $this->id .']" id="tracker_report_criteria_'. $this->id .'" value="';
        $criteria_value = $this->getCriteriaValue($criteria);

        if ($criteria_value !== '') {
            $html_purifier = Codendi_HTMLPurifier::instance();
            $html         .= $html_purifier->purify($criteria_value, CODENDI_PURIFIER_CONVERT_HTML);
        }

        $html .= '" />';
        return $html;
    }

    public function getCriteriaWhere($criteria) {
        return '';
    }

    public function getQueryFrom() {
        $R1 = 'R1_'. $this->id;
        $R2 = 'R2_'. $this->id;

        return "LEFT JOIN ( tracker_changeset_value AS $R1
                    INNER JOIN tracker_changeset_value_float AS $R2 ON ($R2.changeset_value_id = $R1.id)
                ) ON ($R1.changeset_id = c.id AND $R1.field_id = ". $this->id ." )";
    }

    protected $pattern = '[+\-]?\d+(?:\.\d+)?(?:[eE][+\-]?\d+)?';
    protected function cast($value) {
        return (float)$value;
    }
    protected function buildMatchExpression($field_name, $criteria_value) {
        return parent::buildMatchExpression($field_name, $criteria_value);
    }

    protected function getCriteriaDao() {
        return new Tracker_Report_Criteria_Float_ValueDao();
    }

    /**
     * Returns a string representing the float value of $value
     *
     * @param int   $artifact_id  the Id of the artifact
     * @param int   $changeset_id the Id of the changeset
     * @param float $value        the value of the float field (or null if none)
     *
     * @return string the string value of the float field (or '' if none)
     */
    public function fetchChangesetValue($artifact_id, $changeset_id, $value, $report=null, $from_aid = null) {
        if ($value === null) {
            return '';
        } else {
            return number_format($value, self::FLOAT_DECIMALS, '.', '');
        }
    }

    protected function getValueDao() {
        return new Tracker_FormElement_Field_Value_FloatDao();
    }
    protected function getDao() {
        return new Tracker_FormElement_Field_FloatDao();
    }

    /**
     * @return the label of the field (mainly used in admin part)
     */
    public static function getFactoryLabel() {
        return $GLOBALS['Language']->getText('plugin_tracker_formelement_admin','float');
    }

    /**
     * @return the description of the field (mainly used in admin part)
     */
    public static function getFactoryDescription() {
        return $GLOBALS['Language']->getText('plugin_tracker_formelement_admin','float_description');
    }

    /**
     * @return the path to the icon
     */
    public static function getFactoryIconUseIt() {
        return $GLOBALS['HTML']->getImagePath('ic/ui-text-field-float.png');
    }

    /**
     * @return the path to the icon
     */
    public static function getFactoryIconCreate() {
        return $GLOBALS['HTML']->getImagePath('ic/ui-text-field-float--plus.png');
    }

    /**
     * Fetch the html code to display the field value in tooltip
     *
     * @param Tracker_Artifact $artifact
     * @param Tracker_Artifact_ChangesetValue_Float $value The changeset value of this field
     * @return string The html code to display the field value in tooltip
     */
    protected function fetchTooltipValue(Tracker_Artifact $artifact, Tracker_Artifact_ChangesetValue $value = null) {
        $html = '';
        if ($value) {
            $html .= $value->getFloat();
        }
        return $html;
    }

    /**
     * @return string the i18n error message to display if the value submitted by the user is not valid
     */
    protected function getValidatorErrorMessage() {
        return $this->getLabel() . ' ' . $GLOBALS['Language']->getText('plugin_tracker_common_artifact', 'error_float_value');
    }

    /**
     * Get the value of this field
     *
     * @param Tracker_Artifact_Changeset $changeset   The changeset (needed in only few cases like 'lud' field)
     * @param int                        $value_id    The id of the value
     * @param boolean                    $has_changed If the changeset value has changed from the rpevious one
     *
     * @return Tracker_Artifact_ChangesetValue or null if not found
     */
    public function getChangesetValue($changeset, $value_id, $has_changed) {
        $changeset_value = null;
        if ($row = $this->getValueDao()->searchById($value_id, $this->id)->getRow()) {
            $float_row_value = $row['value'];
            if ($float_row_value !== null) {
                $float_row_value = (float)$float_row_value;
            }
            $changeset_value = new Tracker_Artifact_ChangesetValue_Float($value_id, $changeset, $this, $has_changed, $float_row_value);
        }
        return $changeset_value;
    }

    /**
     * @return FromWhere
     */
    public function getExpertEqualFromWhere($value, $suffix)
    {
        $changeset_value_float_alias = 'CVFloat_'. $this->id .'_'. $suffix;
        if ($value === '') {
            $condition = "1";
        } else {
            $condition = "$changeset_value_float_alias.value = ".$this->escapeFloat($value);
        }

        return $this->getExpertFromWhere($suffix, $condition);
    }

    /**
     * @return FromWhere
     */
    public function getExpertNotEqualFromWhere($value, $suffix)
    {
        $changeset_value_float_alias = 'CVFloat_'. $this->id .'_'. $suffix;
        if ($value === '') {
            $condition = "$changeset_value_float_alias.value IS NOT NULL";
        } else {
            $condition = "($changeset_value_float_alias.value IS NULL
                OR $changeset_value_float_alias.value != ".$this->escapeFloat($value).")";
        }

        return $this->getExpertFromWhere($suffix, $condition);
    }

    private function getExpertFromWhere($suffix, $condition)
    {
        $changeset_value_alias       = 'CV_'. $this->id .'_'. $suffix;
        $changeset_value_float_alias = 'CVFloat_'. $this->id .'_'. $suffix;
        $from = " LEFT JOIN (
                        tracker_changeset_value AS $changeset_value_alias
                        INNER JOIN tracker_changeset_value_float AS $changeset_value_float_alias
                         ON ($changeset_value_float_alias.changeset_value_id = $changeset_value_alias.id
                             AND $condition
                         )
                     ) ON ($changeset_value_alias.changeset_id = c.id AND $changeset_value_alias.field_id = $this->id )";

        $where = "$changeset_value_alias.changeset_id IS NOT NULL";

        return new FromWhere($from, $where);
    }

    public function accept(Tracker_FormElement_FieldVisitor $visitor) {
        return $visitor->visitFloat($this);
    }

    private function escapeFloat($value)
    {
        return CodendiDataAccess::instance()->escapeFloat($value);
    }
}
