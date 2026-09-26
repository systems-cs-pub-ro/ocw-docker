<?php
/**
 * OCW Secret plugin: Hide wiki contents (solutions / hints)
 *
 * Syntax:
 *   Hide contents:
 *     <secret> ... </secret>
 *     <solution> ... </solution>
 *     <solution -hidden> ... </solution>   (click to reveal)
 *     <secret -en> ... </secret>           (English button texts)
 *   Reveal everything from this point on:
 *     ~~NOSECRET~~  /  ~~SHOWSOLUTION~~
 *
 * Content is hidden from users without delete rights on the current page.
 * Ported from the 2012 version to the DokuWiki 2026-07-14c (Mort) plugin API.
 *
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author   Florin Stancu <florin.stancu@upb.ro>, Mircea Bardac <mircea@bardac.net>, \
 *           Sergiu Costea <sergiu.costea@gmail.com>
 */

class syntax_plugin_ocwsecret extends \dokuwiki\Extension\SyntaxPlugin
{
    /** @var bool once a ~~NOSECRET~~ marker was seen, keep showing content */
    private bool $secret = true;
    /** @var string[] doc snapshots of open hidden blocks (nesting-safe) */
    private array $buffers = [];

    function getType()
    {
        return 'container';
    }

    function getAllowedTypes()
    {
        return ['container', 'baseonly', 'substition', 'protected', 'disabled', 'formatting', 'paragraphs'];
    }

    function getPType()
    {
        return 'block';
    }

    function getSort()
    {
        return 194;
    }

    // allow nesting of own blocks (same idiom as the core "hidden" plugin)
    function accepts($mode)
    {
        if ($mode === substr(static::class, 7)) return true;
        return parent::accepts($mode);
    }

    function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('~~NOSECRET~~', $mode, 'plugin_ocwsecret');
        $this->Lexer->addSpecialPattern('~~SHOWSOLUTION~~', $mode, 'plugin_ocwsecret');
        $this->Lexer->addEntryPattern('<solution(?![a-zA-Z]).*?>(?=.*?</solution>)', $mode, 'plugin_ocwsecret');
        $this->Lexer->addEntryPattern('<secret(?![a-zA-Z]).*?>(?=.*?</secret>)', $mode, 'plugin_ocwsecret');
    }

    function postConnect()
    {
        $this->Lexer->addExitPattern('</solution>', 'plugin_ocwsecret');
        $this->Lexer->addExitPattern('</secret>', 'plugin_ocwsecret');
    }

    function handle($match, $state, $pos, Doku_Handler $handler)
    {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $data = ['state' => $state, 'lang' => 'ro', 'hidden' => false];
                if (preg_match('/-en/i', $match)) $data['lang'] = 'en';
                if (preg_match('/-hidden/i', $match)) $data['hidden'] = true;
                return $data;
            case DOKU_LEXER_SPECIAL:
                $this->secret = false;
                return ['state' => $state];
            case DOKU_LEXER_UNMATCHED:
                // parse the inner wiki markup like any other container
                // raw inner content, parsed as wiki markup by the cdata renderer pass
                $handler->addCall('cdata', [$match], $pos);
                return ['state' => $state];
            case DOKU_LEXER_EXIT:
                return ['state' => $state];
        }
        return false;
    }

    function render($format, Doku_Renderer $renderer, $data)
    {
        if ($format !== 'xhtml') return false;

        global $INFO;
        $visible = !$this->secret || auth_quickaclcheck($INFO['id']) >= AUTH_DELETE;

        switch ($data['state']) {
            case DOKU_LEXER_ENTER:
                if (!$visible) {
                    $this->buffers[] = $renderer->doc;
                    break;
                }
                [$solText, $showText, $hideText] = $this->texts($data['lang'], $data['hidden']);
                $titleClass = 'solution-title' . ($data['hidden'] ? ' solution-hidden-title' : '');
                $bodyClass = 'solution-content' . ($data['hidden'] ? ' solution-hidden-content' : '');
                $renderer->doc .= '<div class="ocw-secret"><div class="' . $titleClass . '">';
                $renderer->doc .= '<span class="title-text">' . $solText . '</span></div>';
                if ($data['hidden']) {
                    $renderer->doc .= '<div class="hide-text">' . $hideText . '</div>'
                                   . '<div class="show-text">' . $showText . '</div>';
                }
                $renderer->doc .= '<div class="' . $bodyClass . '">';
                break;
            case DOKU_LEXER_EXIT:
                if (!$visible) {
                    // drop the rendered inner content, keep the page cache out
                    $renderer->doc = array_pop($this->buffers);
                    $renderer->nocache();
                    break;
                }
                $renderer->doc .= '</div></div>';
                break;
        }
        return true;
    }

    /** @return array{0:string,1:string,2:string} [title, show text, hide text] */
    private function texts(string $lang, bool $hidden): array
    {
        if ($lang === 'en') {
            return $hidden
                ? ['Show solution', 'Show solution', 'Hide solution']
                : ['Solution', '', ''];
        }
        return $hidden
            ? ['Afișează rezolvarea', 'Afișează rezolvarea', 'Ascunde rezolvarea']
            : ['Rezolvare', '', ''];
    }
}

