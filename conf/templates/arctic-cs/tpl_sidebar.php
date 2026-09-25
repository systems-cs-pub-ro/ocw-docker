<?php
/**
 * Template sidebar (Arctic CS).
 *
 * Prints the sidebar if found, otherwise a standard sidebar placeholder.
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

use function dokuwiki\template\arctic_cs\tpl_findsidebar;

$sidebarId = tpl_findsidebar($conf['sidebar']);
$showSidebar = ($ACT == 'show');
?>
<?php if ($showSidebar) : ?>
    <!-- ********** Sidebar ********** -->
    <nav id="dokuwiki__aside" aria-label="<?php echo $lang['sidebar']
    ?>"><div class="pad aside include group">
        <h3 class="toggle"><?php echo $lang['sidebar'] ?></h3>
        <div class="content"><div class="group">
            <?php tpl_flush() ?>
            <?php tpl_searchform() ?>
            <?php tpl_toc() ?>
            <?php tpl_includeFile('sidebarheader.html') ?>
			<?php if ($sidebarId !== false) : ?>
            <?php tpl_include_page($sidebarId, true, true) ?>
			<?php endif ?>
            <?php tpl_includeFile('sidebarfooter.html') ?>
        </div></div>
    </div></nav><!-- /aside -->
<?php endif; ?>

