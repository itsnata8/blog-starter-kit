<!-- Limit to 2 Links each side of the current page -->
<?php $pager->setSurroundCount(2)  ?>
<!-- END-->

<div class="row">
    <!-- Pagination -->
    <div class="col-12">
        <ul class="pagination justify-content-center">
            <!-- Previous and First Links if available -->
            <?php if ($pager->hasPrevious()): ?>
                <li class="page-item">
                    <a href="<?= $pager->getFirst() ?>" class="page-link">First</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="Pagination Arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor"
                            viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z">
                            </path>
                        </svg>
                    </a>
                </li>
            <?php endif; ?>
            <!-- End of Previous and First -->

            <!-- Page Links -->
            <?php foreach ($pager->links() as $link): ?>
                <li class="page-item <?= $link['active'] ? 'active' : '' ?>"><a class="page-link"
                        href="<?= $link['uri'] ?>"><?= $link['title'] ?></a></li>
            <?php endforeach; ?>
            <!-- End of Page Links -->

            <!-- Next and Last Page -->
            <?php if ($pager->hasNext()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getNext() ?>" aria-label="Pagination Arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor"
                            viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z">
                            </path>
                        </svg>
                    </a>
                </li>
                <li class="page-item">
                    <a href="<?= $pager->getLast() ?>" class="page-link">Last</a>
                </li>
            <?php endif; ?>
            <!-- End of Next and Last Page -->
        </ul>
    </div>


</div>