<?php
/**
 * DokuWiki OCW Template
 *
 * @link     https://ocw.cs.pub.ro/
 * @author   Florin Stancu <me@niflo.ro>
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

if (!defined('DOKU_INC')) die(); /* must be run from within DokuWiki */

require_once(__DIR__ . "/tpl_common.php");

use dokuwiki\template\arctic_cs\{BotPageMenu,SiteMenu};
use function dokuwiki\template\arctic_cs\tpl_render_menu_items;

$hasSidebar = true;
$showSidebar = $hasSidebar && ($ACT == 'show');
?><!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<head>
    <meta charset="utf-8" />
    <title><?php tpl_pagetitle() ?> [<?php echo strip_tags($conf['title']) ?>]</title>
    <?php tpl_metaheaders() ?>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <?php echo tpl_favicon(['favicon', 'mobile']) ?>
    <?php tpl_includeFile('meta.html') ?>
</head>

<body>
    <div id="dokuwiki__site" class="site <?php echo tpl_classes(); ?> <?php
        echo ($showSidebar) ? 'showSidebar' : ''; ?> <?php echo ($hasSidebar) ? 'hasSidebar' : ''; ?> <?php
        echo (tpl_getConf('sidebar') == 'right' && $showSidebar) ? 'sidebarRight' : ''; ?>">

        <?php include(__DIR__ . '/tpl_header.php') ?>

        <div class="wrapper group">
            <?php include(__DIR__ . '/tpl_sidebar.php'); ?>

            <!-- ********** CONTENT ********** -->
            <main id="dokuwiki__content"><div class="pad group">
                <?php html_msgarea() ?>

                <div class="page group">
                    <?php tpl_flush() ?>
                    <?php tpl_includeFile('pageheader.html') ?>
                    <!-- wikipage start -->
                    <?php tpl_content() ?>
                    <!-- wikipage stop -->
                    <?php tpl_includeFile('pagefooter.html') ?>
                </div>
            </div></main><!-- /content -->

            <div class="docInfo"><?php tpl_pageinfo() ?></div>
            <?php tpl_flush() ?>
            <hr class="a11y" />

            <div id="bar__bottom">
                <ul class="arctic_inline_menu">
                    <?php echo tpl_render_menu_items((new SiteMenu())->getItems()); ?>
                </ul>
            </div>

        </div><!-- /wrapper -->

        <?php include(__DIR__ . '/tpl_footer.php') ?>
    </div></div><!-- /site -->

    <div class="no"><?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?></div>
    <div id="screen__mode" class="no"></div><?php /* helper to detect CSS media query in script.js */ ?>
</body>
</html>
