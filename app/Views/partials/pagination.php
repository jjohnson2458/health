<?php if (isset($pagination) && $pagination['lastPage'] > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination pagination-sm justify-content-center">
        <?php if ($pagination['page'] > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $pagination['page'] - 1 ?>">&laquo;</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
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
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
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
                <a class="page-link" href="?page=<?= $pagination['page'] + 1 ?>">&raquo;</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
        <?php endif; ?>
    </ul>
</nav>
<p class="text-center text-muted small">
    <?= e(__('pagination.showing')) ?> <?= (($pagination['page'] - 1) * $pagination['perPage']) + 1 ?>-<?= min($pagination['page'] * $pagination['perPage'], $pagination['total']) ?>
    <?= e(__('pagination.of')) ?> <?= $pagination['total'] ?>
</p>
<?php endif; ?>
