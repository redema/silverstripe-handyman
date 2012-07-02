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

class DataObjectVersionedMethodDecorator extends DataObjectDecorator {
	
	public function doPublish() {
		if ($this->owner->hasMethod('canPublish') &&
				!$this->owner->canPublish())
			return false;
		
		$original = Versioned::get_one_by_stage($this->owner->ClassName, 'Live',
			"\"{$this->owner->ClassName}\".\"ID\" = {$this->owner->ID}");
		$original = $original? $original: new $this->owner->ClassName;
		$this->owner->invokeWithExtensions('onBeforePublish', $original);
		$this->owner->publish('Stage', 'Live');
		$this->owner->invokeWithExtensions('onAfterPublish', $original);
		
		return true;
	}
	
	public function doUnpublish() {
		if ($this->owner->hasMethod('canDeleteFromLive') &&
				!$this->owner->canDeleteFromLive())
			return false;
		
		$this->owner->invokeWithExtensions('onBeforeUnpublish');
		$this->owner->deleteFromStage('Live');
		$this->owner->invokeWithExtensions('onAfterUnpublish');
		
		return true;
	}
}

