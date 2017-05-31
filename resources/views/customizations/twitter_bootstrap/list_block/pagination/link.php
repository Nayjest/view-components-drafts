<?php if ($isCurrent): ?>
    <?php
    $isActive = (!in_array($title, ['«', '»']))
    ?>
    <li data-disabled="1" class="<?= $isActive ? 'active' : 'disabled' ?>">
        <span><?= $title ?></span>
    </li>
<?php else: ?>
    <li>
        <a href="<?= $url ?>"><?= $title ?></a>
    </li>
<?php endif ?>