<?= $this->extend('frontend/layout/pages-layout'); ?>

<?= $this->section('page_meta'); ?>
<meta name="robots" content="noindex, nofollow" />
<meta name="title" content="<?= get_settings()->blog_title ?>" />
<meta name="author" content="<?= get_settings()->blog_meta_description ?>" />
<link rel="canonical" href="<?= base_url() ?>" />
<meta property="og:title" content="<?= get_settings()->blog_title ?>" />
<meta property="og:description" content="<?= get_settings()->blog_meta_description ?>" />
<meta property="og:url" content="<?= base_url() ?>" />
<meta property="og:image" content="<?= get_settings()->blog_logo ? '/images/blog/' . get_settings()->blog_logo : '/images/blog/default-logo-dark.png' ?>" />
<meta name="twitter:domain" content="<?= base_url() ?>" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="<?= get_settings()->blog_title ?>" />
<meta name="twitter:description" property="og:description" itemdrop="description" content="<?= get_settings()->blog_meta_description ?>" />
<meta name="twitter:image" content="<?= get_settings()->blog_logo ? '/images/blog/' . get_settings()->blog_logo : '/images/blog/default-logo-dark.png' ?>" />
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="row no-gutters-lg">
    <div class="col-12">
        <h2 class="section-title">Latest Articles</h2>
    </div>
    <div class="col-lg-8 mb-5 mb-lg-0">
        <div class="row">
            <div class="col-12 mb-4">
                <article class="card article-card">
                    <a href="<?= route_to('read-post', get_home_main_latest_post()->slug) ?>">
                        <div class="card-image">
                            <div class="post-info"> <span class="text-uppercase"><?= date_formatter(get_home_main_latest_post()->created_at); ?></span>
                                <span class="text-uppercase"><?= get_reading_time(get_home_main_latest_post()->content); ?></span>
                            </div>
                            <img loading="lazy" decoding="async" src="/images/posts/<?= get_home_main_latest_post()->featured_image ?>" alt="Post Thumbnail" class="w-100">
                        </div>
                    </a>
                    <div class="card-body px-0 pb-1">
                        <h2 class="h1"><a class="post-title" href="<?= route_to('read-post', get_home_main_latest_post()->slug) ?>"><?= get_home_main_latest_post()->title; ?></a></h2>
                        <p class="card-text"><?= limit_words(get_home_main_latest_post()->content, 35); ?></p>
                        <div class="content"> <a class="read-more-btn" href="<?= route_to('read-post', get_home_main_latest_post()->slug) ?>">Read Full Article</a>
                        </div>
                    </div>
                </article>
            </div>
            <?php if (count(get_6_home_latest_post()) > 0): ?>
                <?php foreach (get_6_home_latest_post() as $post): ?>
                    <div class="col-md-6 mb-4">
                        <article class="card article-card article-card-sm h-100">
                            <a href="<?= route_to('read-post', $post->slug) ?>">
                                <div class="card-image">
                                    <div class="post-info"> <span class="text-uppercase"><?= date_formatter($post->created_at); ?></span>
                                        <span class="text-uppercase"><?= get_reading_time($post->content); ?></span>
                                    </div>
                                    <img loading="lazy" decoding="async" src="frontend/images/post/post-2.jpg" alt="Post Thumbnail" class="w-100">
                                </div>
                            </a>
                            <div class="card-body px-0 pb-0">
                                <h2><a class="post-title" href="<?= route_to('read-post', $post->slug) ?>"><?= $post->title; ?></a></h2>
                                <p class="card-text"><?= limit_words($post->content, 13); ?></p>
                                <div class="content"> <a class="read-more-btn" href="<?= route_to('read-post', $post->slug) ?>">Read Full Article</a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="widget-blocks">
            <div class="row">
                <div class="col-lg-12">
                    <div class="widget">
                        <div class="widget-body">
                            <img loading="lazy" decoding="async" src="frontend/images/author.jpg" alt="About Me" class="w-100 author-thumb-sm d-block">
                            <h2 class="widget-title my-3">Hootan Safiyari</h2>
                            <p class="mb-3 pb-2">Hello, I’m Hootan Safiyari. A Content writter, Developer and Story teller. Working as a Content writter at CoolTech Agency. Quam nihil …</p> <a href="about.html" class="btn btn-sm btn-outline-primary">Know
                                More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-6">
                    <div class="widget">
                        <h2 class="section-title mb-3">Recommended</h2>
                        <div class="widget-body">
                            <div class="widget-list">
                                <article class="card mb-4">
                                    <div class="card-image">
                                        <div class="post-info"> <span class="text-uppercase">1 minutes read</span>
                                        </div>
                                        <img loading="lazy" decoding="async" src="frontend/images/post/post-9.jpg" alt="Post Thumbnail" class="w-100">
                                    </div>
                                    <div class="card-body px-0 pb-1">
                                        <h3><a class="post-title post-title-sm" href="article.html">Portugal and France Now
                                                Allow Unvaccinated Tourists</a></h3>
                                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor …</p>
                                        <div class="content"> <a class="read-more-btn" href="article.html">Read Full Article</a>
                                        </div>
                                    </div>
                                </article>
                                <a class="media align-items-center" href="article.html">
                                    <img loading="lazy" decoding="async" src="frontend/images/post/post-2.jpg" alt="Post Thumbnail" class="w-100">
                                    <div class="media-body ml-3">
                                        <h3 style="margin-top:-5px">These Are Making It Easier To Visit</h3>
                                        <p class="mb-0 small">Heading Here is example of hedings. You can use …</p>
                                    </div>
                                </a>
                                <a class="media align-items-center" href="article.html"> <span class="image-fallback image-fallback-xs">No Image Specified</span>
                                    <div class="media-body ml-3">
                                        <h3 style="margin-top:-5px">No Image specified</h3>
                                        <p class="mb-0 small">Lorem ipsum dolor sit amet, consectetur adipiscing …</p>
                                    </div>
                                </a>
                                <a class="media align-items-center" href="article.html">
                                    <img loading="lazy" decoding="async" src="frontend/images/post/post-5.jpg" alt="Post Thumbnail" class="w-100">
                                    <div class="media-body ml-3">
                                        <h3 style="margin-top:-5px">Perfect For Fashion</h3>
                                        <p class="mb-0 small">Lorem ipsum dolor sit amet, consectetur adipiscing …</p>
                                    </div>
                                </a>
                                <a class="media align-items-center" href="article.html">
                                    <img loading="lazy" decoding="async" src="frontend/images/post/post-9.jpg" alt="Post Thumbnail" class="w-100">
                                    <div class="media-body ml-3">
                                        <h3 style="margin-top:-5px">Record Utra Smooth Video</h3>
                                        <p class="mb-0 small">Lorem ipsum dolor sit amet, consectetur adipiscing …</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-6">
                    <?php include('partials/sidebar-categories.php') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>