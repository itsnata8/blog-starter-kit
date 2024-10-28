<div class="widget">
    <h2 class="section-title mb-3">Recommended</h2>
    <div class="widget-body">
        <div class="widget-list">
            <?php foreach (sidebar_latest_post() as $post): ?>
                <a class="media align-items-center" href="<?= route_to('read-post', $post->slug) ?>">
                    <img loading="lazy" decoding="async" src="/images/posts/thumb_<?= $post->featured_image ?>" alt="Post Thumbnail" class="w-100">
                    <div class="media-body ml-3">
                        <h3 style="margin-top:-5px"><?= $post->title; ?></h3>
                        <p class="mb-0 small"><?= limit_words($post->content, 6); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>