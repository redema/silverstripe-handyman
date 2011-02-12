<?php

/**
 * Copyright 2011 Charden Reklam Östersund AB (http://charden.se/)
 * Erik Edlund <erik@charden.se>
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 * * Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * 
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * 
 * * Neither the name of Charden Reklam, nor the names of its contributors may be
 *   used to endorse or promote products derived from this software without specific
 *   prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * <h1>Summary</h1>
 * 
 * Enforce values on DataObject db fields.
 */
class DataObjectEnforceDBValueDecorator extends DataObjectDecorator {
	
	/**
	 * Get a map where the keys are db field names mapped to the
	 * values which they should be assigned.
	 * 
	 * @return array
	 */
	public function getEnforcedValues() {
		$values = array();
		foreach ((array)$this->owner->stat('enforce_db_value') as $field => $value) {
			if (is_string($value) && substr($value, 0, 2) == '->') {
				$method = substr($value, 2);
				$values[$field] = $this->owner->$method();
			} else {
				$values[$field] = $value;
			}
		}
		return $values;
	}
	
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		foreach ($this->getEnforcedValues() as $field => $value)
			$this->owner->$field = $value;
	}
	
	/**
	 * Make a readonly transformation on all fields in the given
	 * FieldSet which have a name corresponding to a key in the
	 * given $values array.
	 * 
	 * @param FieldSet &$fields
	 * @param array $values
	 */
	public function updateFieldSet(FieldSet &$fields, array $values) {
		foreach ($values as $field => $value) {
			if ($fields->dataFieldByName($field))
				$fields->makeFieldReadonly($field);
		}
	}
	
	public function updateCMSFields(FieldSet &$fields) {
		$this->updateFieldSet($fields, $this->getEnforcedValues());
	}
	
	public function updateFrontEndFields(FieldSet &$fields) {
		$this->updateFieldSet($fields, $this->getEnforcedValues());
	}
	
	
}

