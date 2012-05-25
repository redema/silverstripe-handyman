<?php

/**
 * Copyright (c) 2012, Redema AB - http://redema.se/
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
 * * Neither the name of Redema, nor the names of its contributors may be used
 *   to endorse or promote products derived from this software without specific
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
 */
class DataObjectEnforceDBValueDecoratorTest extends SapphireTest {
	
	public static $fixture_file = 'handyman/tests/DataObjectEnforceDBValueDecoratorTest.yml';
	
	public function testEnforcedValues() {
		$test = new DataObjectEnforceDBValueDecoratorTest_Test();
		$test->Field1 = 'Value';
		$test->Field2 = 'Value';
		$testID = $test->write();
		
		$test = DataObject::get_by_id('DataObjectEnforceDBValueDecoratorTest_Test', $testID);
		
		$this->assertEquals('StaticValue', $test->Field1, 'Static value not saved');
		$this->assertEquals('DynamicValue', $test->Field2, 'Dynamic value not saved');
	}
	
	public function testFormFieldReadonlyTransformation() {
		foreach (array('getCMSFields', 'getFrontEndFields') as $fieldMethod) {
			$fields = singleton('DataObjectEnforceDBValueDecoratorTest_Test')->$fieldMethod();
			foreach (array('Field1', 'Field2') as $field) {
				$field = $fields->dataFieldByName($field);
				$this->assertInstanceOf('FormField', $field);
				$this->assertTrue($field->isReadonly());
			}
		}
	}
	
}

class DataObjectEnforceDBValueDecoratorTest_Test extends DataObject {
	
	public static $db = array(
		'Field1' => 'Text',
		'Field2' => 'Text'
	);
	
	public static $enforce_db_value = array(
		'Field1' => 'StaticValue',
		'Field2' => '->Field2Value'
	);
	
	public function Field2Value() {
		return 'DynamicValue';
	}
	
}

