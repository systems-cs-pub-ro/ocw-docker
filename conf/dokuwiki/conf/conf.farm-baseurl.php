<?php
/**
 * protected overrides for the docker environment
 */

/* For farm animals, use /courses/NAME as base URL */
if (defined("DOKU_FARM_ANIMAL") && !empty(DOKU_FARM_ANIMAL)) {
	// we will always use the /courses/<ID> convention as base directory
	$conf['basedir'] = '/courses/' . DOKU_FARM_ANIMAL;
}

