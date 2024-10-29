<?= $this->extend('frontend/layout/pages-layout'); ?>
<?= $this->section('content'); ?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4 border-bottom border-primary d-inline-block">Tag: <?= $tag; ?></h1>
    </div>
    <div class="col-lg-8 mb-5 mb-lg-0">
        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 mb-4">
                    <article class="card article-card article-card-sm h-100">
                        <a href="<?= route_to('read-post', $post->slug) ?>">
                            <div class="card-image">
                                <div class="post-info"> <span class="text-uppercase"><?= date_formatter($post->created_at) ?></span>
                                    <span class="text-uppercase"><?= get_reading_time($post->content) ?></span>
                                </div>
                                <img loading="lazy" decoding="async" src="/images/posts/resized_<?= $post->featured_image ?>" alt="Post Thumbnail" class="w-100" width="420" height="280">
                            </div>
                        </a>
                        <div class="card-body px-0 pb-0">
                            <h2><a class="post-title" href="<?= route_to('read-post', $post->slug) ?>"><?= $post->title ?></a></h2>
                            <p class="card-text"><?= limit_words($post->content); ?></p>
                            <div class="content"> <a class="read-more-btn" href="<?= route_to('read-post', $post->slug) ?>">Read Full Article</a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach ?>
        </div>
        <div class="col-12">
            <div class="row">
                <?php if ($pager && $pager->getPageCount() > 1): ?>
                    <?= $pager->makeLinks($page, $perPage, $total, 'custom_pagination_view'); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="widget-blocks">
            <div class="row">
                <div class="col-lg-12 col-md-6">
                    <?php include('partials/sidebar-latest-post.php') ?>
                </div>
                <div class="col-lg-12 col-md-6">
                    <?php include('partials/sidebar-categories.php') ?>
                </div>
                <div class="col-lg-12 col-md-6">
                    <?php include('partials/sidebar-tags.php') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>