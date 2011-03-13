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
 * Resize simple shapes.
 */
class Resize {
	
	const IF_BIGGER  = 0;
	const IF_SMALLER = 1;
	const ALWAYS	 = 2;
	
	/**
	 * Resize a rectangle.
	 * 
	 * @param integer $width
	 * @param integer $height
	 * @param integer $targetWidth
	 * @param integer $targetHeight
	 * @param integer $when
	 * 
	 * @return array(integer=width, integer=height)
	 */
	public static function rectangle($sourceWidth, $sourceHeight,
			$targetWidth, $targetHeight, $when = Resize::ALWAYS) {
		foreach (array('sourceWidth', 'sourceHeight', 'targetWidth', 'targetHeight') as $paramVar) {
			$$paramVar = ceil($$paramVar);
			if ($$paramVar < 0)
				trigger_error("{$paramVar} is negative", E_USER_ERROR);
		}
		$mustResize = (
			(Resize::ALWAYS == $when) ||
			(Resize::IF_BIGGER == $when &&
				(($sourceWidth > $targetWidth && $targetWidth > 0) ||
				 ($sourceHeight > $targetHeight && $targetHeight > 0))) ||
			(Resize::IF_SMALLER == $when &&
				(($sourceWidth < $targetWidth && $targetWidth > 0) ||
				 ($sourceHeight < $targetHeight && $targetHeight > 0)))
		);
		if (!$mustResize) {
			return array($sourceWidth, $sourceHeight);
		}
		
		if ($targetWidth > 0 && $targetHeight < 1) {
			$targetHeight = $sourceHeight * ($targetWidth / $sourceWidth);
		} else if ($targetHeight > 0 && $targetWidth < 1) {
			$targetWidth = $sourceWidth * ($targetHeight / $sourceHeight);
		} else if ($targetWidth < 1 && $targetHeight < 1) {
			$targetWidth = $sourceWidth;
			$targetHeight = $sourceHeight;
		} else {
			// Both $targetWidth and $targetHeight are set, that's okay.
		}
		return array(ceil($targetWidth), ceil($targetHeight));
	}
	
}

class Resize_Embed {
	
	/**
	 * Resize an embed tag by matching all width/height
	 * attributes in the given $markup, using the first pair
	 * to calculate new width/height values and then replacing
	 * the old attributes. This method is quite dumb and will
	 * not handle unexpected or complicated markup well.
	 * 
	 * @param string $markup
	 * @param integer $width
	 * @param integer $height
	 * @param integer $when
	 * 
	 * @return string
	 */
	public static function using_attributes($markup, $width, $height,
			$when = Resize::ALWAYS) {
		// These two variable names must match the two keys in the
		// $search array below.
		$attrWidth = 0;
		$attrHeight = 0;
		
		// Regexes to match width and height attributes in markup.
		$search = array(
			'attrWidth' => '/width(\s*)=\1"?\d+"?/i',
			'attrHeight' => '/height(\s*)=\1"?\d+"?/i'
		);
		
		$matches = array();
		
		// Find all width and height attributes in the markup given.
		foreach ($search as $key => $pattern) {
			if (preg_match($pattern, $markup, $matches))
				if (preg_match('/\d+/i', $matches[0], $matches))
					$$key = $matches[0];
		}
		list($width, $height) = Resize::rectangle($attrWidth, $attrHeight, $width, $height, $when);
		$replace = array(
			"width=\"{$width}\"",
			"height=\"{$height}\""
		);
		
		$markup = strip_tags($markup, '<iframe><object><param><embed><video>');
		$markup = preg_replace($search, $replace, $markup);
		
		return $markup;
	}
	
}

