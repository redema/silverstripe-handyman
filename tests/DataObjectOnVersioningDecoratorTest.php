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
 * FIXME: This is ugly. Rewrite when there is time to do so.
 */
class DataObjectOnVersioningDecoratorTest extends SapphireTest {
	
	public static $fixture_file = 'handyman/tests/DataObjectOnVersioningDecoratorTest.yml';
	
	protected function assertDataObjectsOnStage($class, $number, $stage = 'Live') {
		$objects = Versioned::get_by_stage($class, $stage);
		if (!$number) {
			$this->assertEmpty($objects);
		} else {
			$this->assertInstanceOf('DataObjectSet', $objects);
			$this->assertEquals($number, $objects->TotalItems());
		}
	}
	
	public function testPublishAndUnpublishWithUpdatableRelations() {
		$testPage = DataObject::get_one('DataObjectOnVersioningDecoratorTest_TestPage',
			"\"URLSegment\" = 'testpage1'");
		$this->assertInstanceOf('DataObjectOnVersioningDecoratorTest_TestPage',
			$testPage);
		
		$tests = $testPage->Tests();
		$this->assertInstanceOf('DataObjectSet', $tests);
		$this->assertEquals(3, $tests->TotalItems());
		
		$testPage->doPublish();
		
		$this->assertDataObjectsOnStage('DataObjectOnVersioningDecoratorTest_Test', 3);
		$this->assertDataObjectsOnStage('DataObjectOnVersioningDecoratorTest_IndirectTest', 2);
		
		$testPage->doUnpublish();
		
		$this->assertDataObjectsOnStage('DataObjectOnVersioningDecoratorTest_Test', 0);
		$this->assertDataObjectsOnStage('DataObjectOnVersioningDecoratorTest_IndirectTest', 0);
	}
	
}

class DataObjectOnVersioningDecoratorTest_TestPage extends Page {
	
	public static $has_many = array(
		'Tests' => 'DataObjectOnVersioningDecoratorTest_Test'
	);
	
}

class DataObjectOnVersioningDecoratorTest_Test extends DataObject {
	
	public static $db = array(
		'Title' => 'Text'
	);
	
	public static $has_one = array(
		'Page' => 'DataObjectOnVersioningDecoratorTest_TestPage'
	);
	
	public static $has_many = array(
		'IndirectTests' => 'DataObjectOnVersioningDecoratorTest_IndirectTest'
	);
	
	public static $has_one_on_versioning = array(
		'Page' => true
	);
	
	public static $extensions = array(
		'DataObjectOnVersioningDecorator',
		'DataObjectVersionedMethodDecorator',
		"Versioned('Stage', 'Live')"
	);
	
}

class DataObjectOnVersioningDecoratorTest_TestExtended extends DataObjectOnVersioningDecoratorTest_Test {
	
	public static $db = array(
		'More' => 'Text'
	);
	
}

class DataObjectOnVersioningDecoratorTest_IndirectTest extends DataObject {
	
	public static $db = array(
		'Title' => 'Text'
	);
	
	public static $has_one = array(
		'Parent' => 'DataObjectOnVersioningDecoratorTest_Test'
	);
	
	public static $has_one_on_versioning = array(
		'Parent' => true
	);
	
	public static $extensions = array(
		'DataObjectOnVersioningDecorator',
		'DataObjectVersionedMethodDecorator',
		"Versioned('Stage', 'Live')"
	);
	
}

