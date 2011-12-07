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
 * Hook into Page_Controller's onAfterInit-event to update
 * i18n with the locale that Translatable selected.
 */
class Page_Controlleri18nExtension extends Extension {
	
	public function onAfterInit() {
		if (Object::has_extension('SiteTree', 'Translatable'))
			i18n::set_locale(Translatable::get_current_locale());
	}
	
}

/**
 * <h1>Summary</h1>
 * 
 * A collection of methods often needed in templates.
 */
class Page_ControllerTemplateUtilsExtension extends Extension {
	
	/**
	 * Get the current environment type from Director.
	 * 
	 * @return string
	 */
	public function EnvironmentType() {
		return Director::get_environment_type();
	}
	
	/**
	 * Helper for rendering the html required for pagination.
	 * 
	 * @return string $name
	 * 
	 * return string
	 */
	public function PaginationFor($name) {
		Requirements::themedCSS('paginationfor');
		return $this->owner->customise(array(
			'Items' => $this->owner->$name()
		))->renderWith(array('PaginationFor'));
	}
	
	private $pageCacheKeyCache = null;
	
	/**
	 * A cache key suitable for partial template caching of
	 * menus. The key is unique for the current page but depends
	 * on the current Versioned stage, all other Pages and the
	 * SiteConfigs.
	 * 
	 * @return string
	 */
	public function PageCacheKey() {
		if (!$this->pageCacheKeyCache) {
			$this->pageCacheKeyCache = implode(':', array(
				$this->owner->data()->ID,
				$this->owner->data()->Link(),
				$this->owner->data()->cacheKeyComponent(),
				$this->owner->data()->Aggregate('Page')->Max('LastEdited'),
				$this->owner->data()->Aggregate('SiteConfig')->Max('LastEdited')
			));
		}
		return $this->pageCacheKeyCache;
	}
	
	public function flushCache() {
		$this->pageCacheKeyCache = null;
	}
	
}

