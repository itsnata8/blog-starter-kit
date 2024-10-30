<?= $this->extend('frontend/layout/pages-layout'); ?>
<?= $this->section('page_meta'); ?>
<meta name="description" content="<?= $post->meta_description ?>" />
<meta name="keywords" content="<?= $post->meta_keywords ?>" />
<link rel="canonical" href="<?= current_url() ?>" />
<meta itemprop="name" content="<?= $post->title ?>" />
<meta itemprop="description" content="<?= $post->meta_description ?>" />
<meta itemprop="image" content="<?= base_url('images/posts/' . $post->featured_image) ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= $post->title ?>" />
<meta property="description" content="<?= $post->meta_description ?>" />
<meta property="og:image" content="<?= base_url('images/posts/' . $post->featured_image) ?>" />
<meta property="og:url" content="<?= current_url() ?>" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:domain" content="<?= base_url() ?>" />
<meta name="twitter:title" property="og:title" itemprop="name" content="<?= $post->title ?>" />
<meta name="twitter:description" property="og:description" itemprop="description" content="<?= $post->meta_description ?>" />
<meta name="twitter:image" content="<?= base_url('images/posts/' . $post->featured_image) ?>" />
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>


<div class="row">
    <div class="col-lg-8 mb-5 mb-lg-0">
        <article>
            <img loading="lazy" decoding="async" src="/images/posts/<?= $post->featured_image ?>" alt="Post Thumbnail" class="w-100">
            <ul class="post-meta mb-2 mt-4">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" style="margin-right:5px;margin-top:-4px" class="text-dark" viewBox="0 0 16 16">
                        <path d="M5.5 10.5A.5.5 0 0 1 6 10h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1-.5-.5z"></path>
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"></path>
                        <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z"></path>
                    </svg> <span><?= date_formatter($post->created_at); ?></span>
                </li>
            </ul>
            <h1 class="my-3"><?= $post->title; ?></h1>
            <?php if (count(get_tags_by_post_id($post->id))): ?>
                <ul class="post-meta mb-4">
                    <?php foreach (get_tags_by_post_id($post->id) as $tag): ?>
                        <li> <a href="<?= route_to('tag-posts', urlencode($tag)) ?>"><?= $tag; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="content text-left">
                <?= $post->content; ?>
            </div>
        </article>

        <div class="prev-next-posts mt-3 mb-3">
            <div class="row justify-content-between p-4">
                <div class="col-md-6 mb-2">
                    <?php if (!empty(get_previous_post($post->id))): ?>
                        <div>
                            <h4>&laquo; Previous</h4>
                            <a href="<?= route_to('read-post', get_previous_post($post->id)->slug) ?>"><?= get_previous_post($post->id)->title; ?></a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-2 text-md-right">
                    <?php if (!empty(get_next_post($post->id))): ?>
                        <div>
                            <h4>Next &raquo;</h4>
                            <a href="<?= route_to('read-post', get_next_post($post->id)->slug) ?>"><?= get_next_post($post->id)->title; ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty(get_related_posts_by_post_id($post->id))): ?>
            <div class="mt-3" id="related-posts">
                <h2>Related posts</h2>
                <div class="widget-body">
                    <div class="widget-list">
                        <?php foreach (get_related_posts_by_post_id($post->id) as $post): ?>
                            <a class="media align-items-center" href="<?= route_to('read-post', $post->slug) ?>">
                                <img loading="lazy" decoding="async" src="/images/posts/thumb_<?= $post->featured_image ?>" alt="Post Thumbnail" class="w-100">
                                <div class="media-body ml-3">
                                    <h3 style="margin-top:-5px"><?= $post->title; ?></h3>
                                    <p class="mb-0 small"><?= limit_words($post->content, 20); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="mt-5">
            <div id="disqus_thread"></div>
            <script type="application/javascript">
                var disqus_config = function() {



                };
                (function() {
                    if (["localhost", "127.0.0.1"].indexOf(window.location.hostname) != -1) {
                        document.getElementById('disqus_thread').innerHTML = 'Disqus comments not available by default when the website is previewed locally.';
                        return;
                    }
                    var d = document,
                        s = d.createElement('script');
                    s.async = true;
                    s.src = '//' + "themefisher-template" + '.disqus.com/embed.js';
                    s.setAttribute('data-timestamp', +new Date());
                    (d.head || d.body).appendChild(s);
                })();
            </script>
            <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a>
            </noscript>
            <a href="https://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="widget-blocks">
            <div class="row">
                <div class="col-lg-12">
                    <div class="widget">
                        <div class="widget-body">
                            <img loading="lazy" decoding="async" src="/frontend/images/author.jpg" alt="About Me" class="w-100 author-thumb-sm d-block">
                            <h2 class="widget-title my-3">Hootan Safiyari</h2>
                            <p class="mb-3 pb-2">Hello, I’m Hootan Safiyari. A Content writter, Developer and Story teller. Working as a Content writter at CoolTech Agency. Quam nihil …</p> <a href="about.html" class="btn btn-sm btn-outline-primary">Know
                                More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-6">
                    <div class="widget">
                        <h2 class="section-title mb-3">Latest posts</h2>
                        <div class="widget-body">
                            <div class="widget-list">
                                <?php foreach (sidebar_latest_post($post->id) as $post): ?>
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