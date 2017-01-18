<?php
/**
 * @var int $total
 * @var int $current
 * @var PaginationTemplate $block
 */
use ViewComponents\ViewComponents\Component\Control\View\PaginationTemplate;

isset($maxLinks) || $maxLinks = 10;
isset($minNumLinksAroundCurrent) || $minNumLinksAroundCurrent = 2;
isset($minNumLinksNearEnd) || $minNumLinksNearEnd = 1;
// without prev & next links
isset($maxNumLinks) || $maxNumLinks = $maxLinks - 2;
?>
<nav data-role="control-container" data-control="pagination">
    <ul>
        <?= $block->renderLink(1, '«') ?>

        <?php if ($total < $maxNumLinks): ?>
            <?= $block->renderLinksRange(1, $total) ?>
        <?php else: ?>
            <?php if ($current + $minNumLinksAroundCurrent < $maxLinks): ?>
                <?php // 1 separator after current page item ?>
                <?= $block->renderLinksRange(1, $current + $minNumLinksAroundCurrent) ?>
                <li><span>...</span></li>
                <?= $block->renderLinksRange($total - $minNumLinksNearEnd, $total) ?>
            <?php elseif ($total - ($current - $minNumLinksAroundCurrent) < $maxLinks): ?>
                <?php // 1 separator before current page item ?>
                <?= $block->renderLinksRange(1, 1 + $minNumLinksNearEnd) ?>
                <li><span>...</span></li>
                <?= $block->renderLinksRange($current - $minNumLinksAroundCurrent, $total) ?>
            <?php else: ?>
                <?php // 2 separators ?>
                <?= $block->renderLinksRange(1, 1 + $minNumLinksNearEnd) ?>
                <li><span>...</span></li>
                <?= $block->renderLinksRange(
                $current - $minNumLinksAroundCurrent,
                $current + $minNumLinksAroundCurrent
            ) ?>
                <li><span>...</span></li>
                <?= $block->renderLinksRange($total - $minNumLinksNearEnd, $total) ?>
            <?php endif ?>
        <?php endif ?>
        <?= $block->renderLink($total, '»') ?>
    </ul>
</nav>