<div class="widget">
    <h2 class="section-title mb-3">Categories</h2>
    <div class="widget-body">
        <ul class="widget-list">
            <?php foreach (get_sidebar_categories() as $category): ?>
                <li><a href="<?= route_to('category-posts', $category->slug) ?>"><?= $category->name; ?><span class="ml-auto">(<?= posts_by_category_id($category->id); ?>)</span></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>