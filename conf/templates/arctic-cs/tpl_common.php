<?php

namespace dokuwiki\template\arctic_cs;

use \dokuwiki\Menu\AbstractMenu;


/** Top page menu actions (just edit). */
class TopPageMenu extends AbstractMenu
{
    protected $view = 'page_edit';
    protected $types = ['Edit'];
    public function loadItems(&$data) {
		parent::loadItems($data);
		$data['items'][] = new \dokuwiki\plugin\dw2pdf\MenuItem;
	}
}
/** Bottom page menu actions (most of them). */
class BotPageMenu extends AbstractMenu
{
    protected $view = 'page';
    protected $types = ['Edit', 'Revisions'];
}

/** Top-right user menu items. */
class UserMenu extends AbstractMenu
{
    protected $view = 'user';
    protected $types = ['Profile', 'Admin', 'Register', 'Login'];
}

/** Bottom-right site menu. */
class SiteMenu extends AbstractMenu
{
    protected $view = 'site';
    protected $types = ['Media', 'Recent', 'Index'];
}

function tpl_render_menu_items($items) {
    $html = '';
    foreach ($items as $item) {
        $html .= "<li class=\"{$item->getType()}\">";
        $attr = buildAttributes($item->getLinkAttributes(false));
        $html .= "<a $attr>";
        $html .= inlineSVG($item->getSvg());
        $html .= '<span>' . hsc($item->getLabel()) . '</span>';
        $html .= "</a>";
        $html .= '</li>';
    }
    return $html;
}

/**
 * Finds the sidebar in the current namespace (determined from $ID) or any
 * higher namespace that can be accessed by the current user,
 * this condition can be overriden by an optional parameter.
 *
 * Modified from page_findnearest to regard the current page as namespace (even
 * if not yet).
 *
 * @param str $page  The name of the page / sidebar.
 * @param bool $useacl only return pages readable by the current user, false to ignore ACLs
 * @return false|string the full page id of the sidebar, false if any
 */
function tpl_findsidebar($page = 'sidebar', $useacl = true)
{
    global $ID;
    $ns = $ID;
    do {
        $pageid = cleanID("$ns:$page");
        if (page_exists($pageid) && (!$useacl || auth_quickaclcheck($pageid) >= AUTH_READ)) {
            return $pageid;
        }
        $ns = getNS($ns);
    } while ($ns !== false);

    return false;
}
