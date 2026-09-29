<?php

use dokuwiki\Extension\ActionPlugin;
use dokuwiki\Extension\EventHandler;
use dokuwiki\Extension\Event;

/**
 * DokuWiki Plugin ocwredirect (Action Component)
 *
 * 302-redirects pages matched by rules in a simple conf file
 * (default: <doku conf dir>/redirect.conf):
 *
 *   # whitespace-separated `pattern target` lines, e.g.:
 *   start  /courses/      # simple redirect
 *   old:*  /courses/old/{{1}}    # target to wildcard match
 *   {{ANIMAL}}:start    {{ANIMAL}}  # expand to FARM ANIMAL ID
 *
 * Patterns use doku page ids (colons or slashes), support `*` wildcards
 * and {{placeholders}}.
 * Targets: a doku page id, a path relative to the DOKU base (leading /)
 * or an absolute URL. First matching rule wins.
 *
 * Note: fires on DOKUWIKI_STARTED, so if `send404` is enabled and the
 * matched page doesn't exist, the 404 status is already sent (the
 * Location header is still emitted).
 */
class action_plugin_ocwredirect extends ActionPlugin
{
    /** @inheritDoc */
    public function register(EventHandler $controller)
    {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'handle');
    }

    /**
     * Event handler for DOKUWIKI_STARTED
     *
     * @param Event $event Event object
     * @return void
     */
    public function handle(Event $event)
    {
        global $ACT, $ID;
        if ($ACT !== 'show') {
            return;
        }

        $id = $ID;
        $rules = $this->_load();
        $initialVars = [
            'ID' => $id,
            'ANIMAL' => (defined('DOKU_FARM_ANIMAL')? DOKU_FARM_ANIMAL : ''),
        ];

        foreach ($rules as [$pattern, $target]) {
            $pattern = $this->_subst($pattern, $initialVars);
            $targetVars = $initialVars;

            // normalize pattern + convert wildcards to PCRE
            $pattern = str_replace('/', ':', $pattern);
            $pattern = ltrim($pattern, ":");
            $regex = '/^' . str_replace('\*', '(.*)', preg_quote($pattern, '/')) . '$/';
            if (!preg_match($regex, $id, $matches)) {
                continue;
            }
            /* print("ocwredirect: $pattern => $target <br>\n"); */
            /* print_r($matches); */
            foreach ($matches as $i => $mat) { $targetVars[(string)$i] = $mat; }

            // never 302 a page to itself
            $target  = $this->_subst($target, $targetVars);
            /* print("REDIRECT = $target<br>\n"); */
            /* die(); */
            if (!preg_match('#^(https?:)?//#i', $target) && !str_starts_with($target, '/') && $target === $id) {
                continue;
            }

            http_response_code(302);
            header('Location: ' . $this->_url($target));
            exit;
        }
    }

    /**
     * Expand {{placeholders}} (single pass).
     */
    protected function _subst($text, $vars)
    {
        return preg_replace_callback(
            '/\{\{([A-Za-z0-9_]+)\}\}/',
            // keep the placeholder if not replaced
            fn ($m) => $vars[$m[1]] ?? ('{{'. $m[1] . '}}'),
            $text
        );
    }

    /**
     * Resolve a target to a URL.
     */
    protected function _url($target)
    {
        global $conf;
        if (preg_match('#^(https?:)?//#i', $target)) {
            return $target;                                          // absolute / protocol-relative URL
        }
        if (str_starts_with($target, '/')) {
            return $conf['basedir'] . ltrim($target, '/');           // relative to DOKU base
        }
        return wl($target, '', true);                                // doku page id
    }

    /**
     * Parse the conf file into individual rules.
     */
    protected function _load()
    {
        $file = $this->getConf('redirectFile');
        $rules = [];

        if (!is_file($file)) {
            return $rules;
        }

        foreach (file($file, FILE_IGNORE_NEW_LINES) as $line) {
            $line = trim($line);
            // skip comments
            if ($line === '' || $line[0] === '#' || $line[0] === ';') {
                continue;
            }
            $parts = preg_split('/\s+/', $line, 2);
            if (count($parts) === 2) {
                $rules[] = [$parts[0], trim($parts[1])];
            } else {
                print("WARNING: ocwredirect: Invalid rule: $line !<br>\n");
            }
        }
        return $rules;
    }
}
