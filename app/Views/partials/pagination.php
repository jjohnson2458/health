<?php if (isset($pagination) && $pagination['lastPage'] > 1): ?>
<?php
    $from = (($pagination['page'] - 1) * $pagination['perPage']) + 1;
    $to = min($pagination['page'] * $pagination['perPage'], $pagination['total']);
?>
<nav aria-label="<?= e(__('pagination.page')) ?> <?= $pagination['page'] ?> <?= e(__('pagination.of')) ?> <?= $pagination['lastPage'] ?>">
    <ul class="pagination pagination-sm justify-content-center mb-1">
        <?php if ($pagination['page'] > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $pagination['page'] - 1 ?>" aria-label="<?= e(__('pagination.previous')) ?>">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link" aria-label="<?= e(__('pagination.previous')) ?>">
                    <span aria-hidden="true">&laquo;</span>
                </span>
            </li>
        <?php endif; ?>

        <?php
        $start = max(1, $pagination['page'] - 2);
        $end = min($pagination['lastPage'], $pagination['page'] + 2);
        if ($start > 1): ?>
            <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i === $pagination['page'] ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>" <?= $i === $pagination['page'] ? 'aria-current="page"' : '' ?>><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $pagination['lastPage']): ?>
            <?php if ($end < $pagination['lastPage'] - 1): ?>
                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
            <?php endif; ?>
            <li class="page-item"><a class="page-link" href="?page=<?= $pagination['lastPage'] ?>"><?= $pagination['lastPage'] ?></a></li>
        <?php endif; ?>

        <?php if ($pagination['page'] < $pagination['lastPage']): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $pagination['page'] + 1 ?>" aria-label="<?= e(__('pagination.next')) ?>">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link" aria-label="<?= e(__('pagination.next')) ?>">
                    <span aria-hidden="true">&raquo;</span>
                </span>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<p class="text-center text-muted small mb-0">
    <?= e(__('pagination.showing')) ?> <?= $from ?> <?= e(__('pagination.to')) ?> <?= $to ?>
    <?= e(__('pagination.of')) ?> <?= $pagination['total'] ?> <?= e(__('pagination.entries')) ?>
</p>
<?php endif; ?>
