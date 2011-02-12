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
 * Uses SiteTree's onAfterPublish()/onAfterUnpublish() hooks
 * to provide automatic publishing/unpublishing for versioned
 * DataObjects connected to the SiteTree through a has_one-
 * relation. Due to Versioned limitations, only the stages
 * "Stage" and "Live" are supported.
 */
class SiteTreeOnVersioningDecorator extends SiteTreeDecorator {
	
	/**
	 * Collect all components (has_one/has_many/many_many) from
	 * extension instances held by $this->owner. This is necessary
	 * to properly deal with DataObjectDecorators.
	 * 
	 * @param string $name has_one|has_many|many_many
	 * 
	 * @return array
	 */
	public function getExtensionComponents($name) {
		$components = array();
		$extensions = $this->owner->getExtensionInstances();
		if ($extensions) foreach ($extensions as $extension) {
			$extension->setOwner($this->owner);
			$extraStatics = $extension->extraStatics();
			if (is_array($extraStatics) && array_key_exists($name, $extraStatics)) {
				$components = array_merge($components, $extraStatics[$name]);
			}
			$extension->clearOwner();
		}
		return $components;
	}
	
	/**
	 * Get all updatable relations from $this->owner.
	 * 
	 * @return array
	 */
	protected function updatableRelations() {
		$updatableRelations = array();
		foreach (array_merge($this->owner->has_many(), $this->getExtensionComponents('has_many')) as
				$hasManyRelation => $hasManyClass) {
			$hasOneOnVersioning = (array)Object::combined_static($hasManyClass, 'has_one_on_versioning');
			foreach ($hasOneOnVersioning as $onUpdateRelation => $process) {
				$hasOneClass = singleton($hasManyClass)->has_one($onUpdateRelation);
				if (in_array($hasOneClass, ClassInfo::ancestry($this->owner->ClassName)) && $process) {
					$updatableRelations[$hasManyRelation] = array(
						$hasManyClass,
						$onUpdateRelation
					);
				}
			}
		}
		return $updatableRelations;
	}
	
	/**
	 * Hook into SiteTree::doPublish().
	 */
	public function onAfterPublish() {
		$fromStage = 'Stage';
		$toStage = 'Live';
		foreach ($this->updatableRelations() as $relationName => $relationData) {
			list($class, $field) = $relationData;
			$liveDataObjects = Versioned::get_by_stage($class, $toStage,
				"\"{$class}\".\"{$field}ID\" = {$this->owner->ID}");
			if ($liveDataObjects) foreach ($liveDataObjects as $object) {
				$object->deleteFromStage($toStage);
			}
			$stageDataObjects = $this->owner->$relationName();
			if ($stageDataObjects) foreach ($stageDataObjects as $object) {
				$object->publish($fromStage, $toStage);
			}
		}
	}
	
	/**
	 * Hook into SiteTree::doUnpublish().
	 */
	public function onAfterUnpublish() {
		foreach ($this->updatableRelations() as $relationName => $relationData) {
			list($class, $field) = $relationData;
			$dataObjects = Versioned::get_by_stage($class, 'Live',
				"\"{$class}\".\"{$field}ID\" = {$this->owner->ID}");
			if ($dataObjects) foreach ($dataObjects as $object) {
				$object->deleteFromStage('Live');
			}
		}
	}
	
	/**
	 * Intenionally unimplemented, use DataObject::write() with
	 * $writeComponents=true instead. The solution is not perfect,
	 * but there is no way to workaround Sapphire's component
	 * cache in this case.
	 */
	public function onAfterWrite() {
		parent::onAfterWrite();
	}
	
}

