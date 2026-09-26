<?php
/**
 * OCW Courses listing plugin
 *
 * Enumerates courses from a PHP file in dokuwiki's conf/ directory
 * (default: conf/courses.php, see dokuwiki/_demo/courses.php):
 *
 *     $courses = [
 *         "<category>" => [
 *             "<code>" => [
 *                 "title" => "Course title",   // required
 *                 "link"  => "https://...",    // optional
 *                 "logos" => "path/or/array",  // optional, overrides logo_patterns
 *             ],
 *         ],
 *     ];
 *
 * Usage in a wiki page:
 *     ~~INC_OCW_COURSES~~
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Florin Stancu <florin.stancu@upb.ro>
 */

class syntax_plugin_ocwcourses extends \dokuwiki\Extension\SyntaxPlugin
{
    function getType() { return 'substition'; }
    function getSort() { return 300; }
    function getPType() { return 'block'; }

    function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('~~INC_OCW_COURSES~~', $mode, 'plugin_ocwcourses');
    }

    function handle($match, $state, $pos, $handler)
    {
        return array();
    }

    function render($format, $renderer, $data)
    {
        if ($format == 'xhtml') {
            $renderer->doc .= $this->renderCourses();
            return true;
        }
        return false;
    }

    // ------------------------------------------------------------------
    // Template-based list rendering
    // ------------------------------------------------------------------

    protected function renderCourses()
    {
        $courses = $this->loadCourses();
        if ($courses === null) {
            return '<div class="ocw-courses-missing">[ocwcourses: conf/' .
                hsc($this->getConf('courses_file')) . ' not found]</div>';
        }

        $template = $this->loadTemplate();
        $out = '<div class="ocw-courses">';
        foreach ($courses as $category => $items) {
            $out .= $this->renderCategory((string) $category);
            $out .= $this->renderItems((array) $items, $template);
        }
        $out .= '</div>';
        return $out;
    }

    /**
     * Load the course list from conf/<courses_file>.
     * The file may set $courses or return the array.
     */
    protected function loadCourses()
    {
        $file = DOKU_INC . 'conf/' . $this->getConf('courses_file');
        if (!file_exists($file)) return null;
        $data = include $file;
        if (is_array($data)) return $data;
        return (isset($courses) && is_array($courses)) ? $courses : null;
    }

    /**
     * Item template page text (template in DokuWiki format), '' if not
     * configured or the page is missing.
     */
    protected function loadTemplate()
    {
        $id = trim((string) $this->getConf('template'));
        if ($id === '') return '';
        $text = (new \dokuwiki\File\PageFile($id))->rawWikiText();
        return is_string($text) ? $text : '';
    }

    protected function renderCategory($category)
    {
        $tpl = trim((string) $this->getConf('category_template'));
        if ($tpl === '') return '';
        return $this->renderWiki(str_replace('{{NAME}}', $category, $tpl));
    }

    protected function renderItems($items, $template)
    {
        $rows = '';
        foreach ($items as $code => $course) {
            if (!is_array($course) || empty($course['title'])) continue;
            $rows .= $this->renderItem((string) $code, $course, $template);
        }
        if ($rows === '') return '';
        // with a custom template the items are standalone blocks; the
        // built-in rows share one table
        return $template !== '' ? $rows : 
            '<div class="level2"><table cellspacing="0" cellpadding="0" class="ocw-courses-list">' . $rows . '</table></div>';
    }

    protected function renderItem($code, $course, $template)
    {
        $title = (string) $course['title'];
        $link = $this->buildLink($code, $course['link'] ?? '');
        $logoURL = $this->findLogo($code, $course['logos'] ?? null);

        if ($template !== '') {
            $content = $link !== '' ? "[[$title|" . str_replace('|', '\|', $link) . ']]' : $title;
            return $this->renderWiki(str_replace(
                array('{{LOGO}}', '{{TITLE}}', '{{CONTENT}}'),
                array($logoURL, $title, $content),
                $template
            ));
        }

        // built-in layout: logo on the left, bold title on the right
        $linkStart = ($link !== '' ? '<a href="' . hsc($link) . '">' : '');
        $linkEnd = ($link !== '' ? '</a>' : '');
        $logoHtml = '';
        if ($logoURL !== '') {
            $w = (int) $this->getConf('logo_width');
            $logoHtml = "$linkStart<img src=\"" . hsc($logoURL) . '" alt="' . hsc($title) . '"' .
                ($w > 0 ? ' width="' . $w . '"' : '') . " />$linkEnd";
        }
        $titleHtml = "$linkStart" . hsc($title) . "$linkEnd";

        return '<tr><td class="ocw-courses-logo">' . $logoHtml . '</td>' .
            '<td class="ocw-courses-title">' . $titleHtml . '</td></tr>';
    }

    /**
     * Find the first existing media file for a course logo.
     * $override (per-course "logos") wins over the configured logo_patterns.
     * {code} is replaced with the course key, {a,b} is brace-expanded.
     */
    protected function findLogo($code, $override)
    {
        $patterns = is_array($override)
            ? $override
            : (is_string($override) && $override !== '' ? array($override) : (array) $this->getConf('logo_patterns'));
        $COURSES = $this->getConf('courses_path');
        foreach ($patterns as $pattern) {
            $pattern = strtr((string)$pattern, [
                '{{code}}' => $code,
                '{{COURSES}}' => $COURSES,
            ]);
            $file = '';
            if (str_starts_with($pattern, ':')) {
                $file = mediaFN($pattern);
                $url = ml($pattern, '', true);
            } elseif (str_starts_with($pattern, "$COURSES/$code/")) {
                // check logo against farm animal media URL
                $url = $pattern;
                $logo_id = str_replace(':', '/', $url);
                $path = explode('/', $logo_id);
                $path = array_filter($path, fn($value) => !empty($value));
                array_splice($path, 0, 2);
                if ($path[0] == '_media') { array_splice($path, 0, 1); }
                $logo_id = implode('/', $path);
                $file = DOKU_FARMDIR . "$code/data/media/" . utf8_encodeFN($logo_id);
            } else {
                print("WARNING: ocwcourses list: invalid pattern $pattern <br>\n");
            }
            if ($file !== '' && file_exists($file)) return $url;
        }
        return '';
    }

    protected function buildLink($code, $courseLink)
    {
        if (!empty($courseLink)) return $courseLink;
        $COURSES = $this->getConf('courses_path');
        return "$COURSES/$code/";
    }

    /** Parse a DokuWiki snippet to xhtml. */
    protected function renderWiki($text)
    {
        $info = array();
        $html = p_render('xhtml', p_get_instructions($text), $info);
        return $html === null ? '' : $html;
    }
}

// vim:ts=4:sw=4:et:
