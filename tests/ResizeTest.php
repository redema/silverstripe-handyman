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

class ResizeTest extends SapphireTest {
	
	public static $fixture_file = 'handyman/tests/ResizeTest.yml';
	
	public function testRectangle() {
		// Normal scaling with preserved aspect ratio.
		list($width, $height) = Resize::rectangle(560, 250, 480, 0);
		$this->assertEquals(215, $height);
		list($width, $height) = Resize::rectangle(256, 128, 0, 500);
		$this->assertEquals(1000, $width);
		
		// Enforce target dimensions (skewing the rectangle).
		$this->assertEquals(array(500, 400), Resize::rectangle(800, 600, 500, 400));
		
		// Test IF_*.
		list($width, $height) = Resize::rectangle(800, 600, 480, 0, Resize::IF_SMALLER);
		$this->assertEquals(600, $height);
		list($width, $height) = Resize::rectangle(480, 320, 800, 0, Resize::IF_BIGGER);
		$this->assertEquals(320, $height);
	}
}

class Resize_EmbedTest extends SapphireTest {
	
	public static $fixture_file = 'handyman/tests/ResizeTest.yml';
	
	public function testYoutube() {
		$oldEmbed = <<<INLINE_HTML
<object width="425" height="349">
	<param name="movie" value="http://www.youtube.com/v/qYodWEKCuGg?fs=1&amp;hl=en_US"></param>
	<param name="allowFullScreen" value="true"></param>
	<param name="allowscriptaccess" value="always"></param>
	<embed src="http://www.youtube.com/v/qYodWEKCuGg?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="349"></embed>
</object>
INLINE_HTML;
		$oldEmbed = Resize_Embed::using_attributes($oldEmbed, 400, 0);
		$this->assertEquals(2, substr_count($oldEmbed, 'width="400"'));
		
		$newEmbed = <<<INLINE_HTML
<iframe title="YouTube video player" width="425" height="349" src="http://www.youtube.com/embed/qYodWEKCuGg" frameborder="0" allowfullscreen></iframe>
INLINE_HTML;
		$newEmbed = Resize_Embed::using_attributes($newEmbed, 400, 0);
		$this->assertEquals(1, substr_count($newEmbed, 'width="400"'));
	}
}

