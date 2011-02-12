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
 */
class SiteTreeOnVersioningDecoratorTest extends SapphireTest {
	
	public static $fixture_file = 'handyman/tests/SiteTreeOnVersioningDecoratorTest.yml';
	
	public function testPublishAndUnpublishWithUpdatableRelations() {
		$testPage = DataObject::get_one('SiteTreeOnVersioningDecoratorTest_TestPage', "\"URLSegment\" = 'testpage1'");
		$this->assertType('SiteTreeOnVersioningDecoratorTest_TestPage', $testPage);
		
		$tests = $testPage->Tests();
		$this->assertType('DataObjectSet', $tests);
		$this->assertEquals(3, $tests->TotalItems());
		
		$testPage->doPublish();
		
		$tests = Versioned::get_by_stage('SiteTreeOnVersioningDecoratorTest_Test', 'Live');
		$this->assertType('DataObjectSet', $tests);
		$this->assertEquals(3, $tests->TotalItems());
		
		$testPage = DataObject::get_one('SiteTreeOnVersioningDecoratorTest_TestPage', "\"URLSegment\" = 'testpage1'");
		$this->assertType('SiteTreeOnVersioningDecoratorTest_TestPage', $testPage);
		$testPage->doUnpublish();
		$tests = Versioned::get_by_stage('SiteTreeOnVersioningDecoratorTest_Test', 'Live');
		$this->assertFalse((bool)$tests);
	}
	
}

class SiteTreeOnVersioningDecoratorTest_TestPage extends Page {
	
	public static $has_many = array(
		'Tests' => 'SiteTreeOnVersioningDecoratorTest_Test'
	);
	
}

class SiteTreeOnVersioningDecoratorTest_Test extends DataObject {
	
	public static $db = array(
		'Title' => 'Text'
	);
	
	public static $has_one = array(
		'Page' => 'SiteTreeOnVersioningDecoratorTest_TestPage'
	);
	
	public static $has_one_on_versioning = array(
		'Page' => true
	);
	
	public static $extensions = array(
		"Versioned('Stage', 'Live')"
	);
	
}

class SiteTreeOnVersioningDecoratorTest_TestExtended extends SiteTreeOnVersioningDecoratorTest_Test {
	
	public static $db = array(
		'More' => 'Text'
	);
	
}

