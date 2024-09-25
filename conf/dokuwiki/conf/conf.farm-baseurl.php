<?php
/**
 * protected overrides for the docker environment
 */

/* For farm animals, use /courses/NAME as base URL */
if (defined("DOKU_FARM_ANIMAL") && !empty(DOKU_FARM_ANIMAL)) {
	// Note: this is a hack to have a homonymous namespace as THE root!
	// Coupled with the custom htaccess rewrite, this should have the intended
	// effects.
	// so we DONT do this: $conf['basedir'] = '/courses/' . DOKU_FARM_ANIMAL . '/';
	// instead, only use the root (as the pseudo-root namespace will be 
	// auto-added by the engine):
	$conf['basedir'] = '/courses/';
}

