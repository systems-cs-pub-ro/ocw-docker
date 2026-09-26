<?php
/**
 * OCW Hidden Content: hide content from all but editors.
 *
 * Syntax: <hidden> ... </hidden>
 *
 * Content is only rendered for users with edit rights on the current page.
 * Ported from the 2010 version to the DokuWiki >= 2026-07-14c (Mort) plugin API.
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Florin Stancu <florin.stancu@upb.ro>, Vlad Dogaru <ddvlad@rosedu.org>
 */

class syntax_plugin_ocwlabhidden extends \dokuwiki\Extension\SyntaxPlugin
{
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
        return 188;
    }

    // allow nesting of own blocks (same idiom as the core "hidden" plugin)
    function accepts($mode)
    {
        if ($mode == substr(get_class($this), 7)) return true;
        return parent::accepts($mode);
    }

    function connectTo($mode)
    {
        $this->Lexer->addEntryPattern('<hidden(?![a-zA-Z]).*?>(?=.*?</hidden>)', $mode, 'plugin_ocwlabhidden');
    }

    function postConnect()
    {
        $this->Lexer->addExitPattern('</hidden>', 'plugin_ocwlabhidden');
    }

    function handle($match, $state, $pos, Doku_Handler $handler)
    {
        switch ($state) {
            case DOKU_LEXER_ENTER:
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
        $visible = ($INFO['perm'] ?? 0) >= AUTH_EDIT;

        switch ($data['state']) {
            case DOKU_LEXER_ENTER:
                if ($visible) {
                    $renderer->doc .= '<div class="ocw-hidden">'.
                        '<div class="hidden-title">Indicații pentru asistenți</div>'.
                        '<div class="hidden-content">';
                } else {
                    $this->buffers[] = $renderer->doc;
                }
                break;

            case DOKU_LEXER_EXIT:
                if ($visible) {
                    $renderer->doc .= '</div></div>';
                } else {
                    // drop the rendered inner content, keep the page cache out
                    $renderer->doc = array_pop($this->buffers);
                    $renderer->nocache();
                }
                break;
        }
        return true;
    }
}
