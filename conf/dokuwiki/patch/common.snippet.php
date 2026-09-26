<?php
// this function will override the one in dokuwiki's common.php
// required to strip the automatically injected namespace == animal ID
// (hey: all in the name of backwards compatibility...)

function idfilter($id, $ue = true)
{
    global $conf;
    /* @var Input $INPUT */
    global $INPUT;

    $id = (string)$id;

    if (defined("DOKU_FARM_ANIMAL") && !empty(DOKU_FARM_ANIMAL)) {
        $prefix = DOKU_FARM_ANIMAL;
        if (substr($id, 0, strlen($prefix)) == $prefix) {
            $id = substr($id, strlen($prefix));
            if (($id[0] ?? '') == ':') {$id = substr($id, 1);}
        } 
    }

    if ($conf['useslash'] && $conf['userewrite']) {
        $id = strtr($id, ':', '/');
    } elseif (
        str_starts_with(strtoupper(PHP_OS), 'WIN') &&
        $conf['userewrite'] &&
        !str_contains($INPUT->server->str('SERVER_SOFTWARE'), 'Microsoft-IIS')
    ) {
        $id = strtr($id, ':', ';');
    }
    if ($ue) {
        $id = rawurlencode($id);
        $id = str_replace('%3A', ':', $id); //keep as colon
        $id = str_replace('%3B', ';', $id); //keep as semicolon
        $id = str_replace('%2F', '/', $id); //keep as slash
    }
    return $id;
}

