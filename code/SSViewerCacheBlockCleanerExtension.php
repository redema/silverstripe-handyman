<?php

class SSViewerCacheBlockCleanerExtension extends Extension {
	
	public function onAfterInit() {
		if (isset($_GET['flush']) && $_GET['flush'] == 'all') {
			// Check for permissions, but do not do anything when denying.
			// SSViewer will handle that.
			if (Director::isDev() || Director::is_cli() || Permission::check('ADMIN')) {
				$cache = SS_Cache::factory('cacheblock');
				$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
			}
		}
	}

}

