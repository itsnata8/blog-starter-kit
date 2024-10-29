<div class="widget">
    <h2 class="section-title mb-3">Tags</h2>
    <div class="widget-body">
        <ul class="widget-list">
            <?php foreach (get_tags() as $tag): ?>
                <li><a href="<?= route_to('tag-posts', urlencode($tag)) ?>"><?= $tag; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>