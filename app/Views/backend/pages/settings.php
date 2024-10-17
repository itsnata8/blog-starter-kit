<?= $this->extend('backend/layout/pages-layout'); ?>
<?= $this->section('content'); ?>

<div class="page-header">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="title">
                <h4>Tabs</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Settings
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="pd-20 card-box mb-4">
    <div class="tab">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-blue" data-toggle="tab" href="#general_settings" role="tab" aria-selected="true">General Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-blue" data-toggle="tab" href="#logo_favicon" role="tab" aria-selected="false">Logo & Favicon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-blue" data-toggle="tab" href="#social_media" role="tab" aria-selected="false">Social media</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="general_settings" role="tabpanel">
                <div class="pd-20">
                    <form action="<?= route_to('admin.update-general-settings') ?>" method="POST" id="general_settings_form">
                        <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" class="ci_csrf_data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Blog title</label>
                                    <input type="text" name="blog_title" class="form-control" placeholder="Enter blog title" value="<?= get_settings()->blog_title; ?>">
                                    <span class="text-danger error-text blog_title_error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Blog email</label>
                                    <input type="text" name="blog_email" class="form-control" placeholder="Enter blog email" value="<?= get_settings()->blog_email; ?>">
                                    <span class="text-danger error-text blog_email_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Blog phone no</label>
                                    <input type="text" name="blog_phone" class="form-control" placeholder="Enter blog phone" value="<?= get_settings()->blog_phone; ?>">
                                    <span class="text-danger error-text blog_phone_error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Blog meta keywords</label>
                                    <input type="text" name="blog_meta_keywords" class="form-control" placeholder="Enter blog meta keywords" value="<?= get_settings()->blog_meta_keywords; ?>">
                                    <span class="text-danger error-text blog_meta_keywords_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Blog meta description</label>
                            <textarea name="blog_meta_description" id="" cols="4" rows="3" placeholder="Enter blog meta description" class="form-control"><?= get_settings()->blog_meta_description; ?></textarea>
                            <span class="text-danger error-text blog_meta_description_error"></span>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="logo_favicon" role="tabpanel">
                <div class="pd-20">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Set blog logo</h5>
                            <div class="mb-2 mt-1" style="max-width:200px;">
                                <img src="<?= get_settings()->blog_logo ? '/images/blog/' . get_settings()->blog_logo : '/images/blog/default-logo-dark.png' ?>" alt="blog image" id="logo-image-preview" class="img-thumbnail">
                            </div>
                            <form action="<?= route_to('admin.update-blog-logo') ?>" method="POST" enctype="multipart/form-data" id="changeBlogLogoForm">
                                <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" class="ci_csrf_data">
                                <div class="mb-2">
                                    <input type="file" name="blog_logo" class="form-control" id="">
                                    <span class="text-danger error-text"></span>
                                </div>
                                <button type="submit" class="btn btn-primary">Change logo</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h5>Set blog favicon</h5>
                            <div class="mb-2 mt-1" style="max-width: 100px;">
                                <img src="<?= get_settings()->blog_favicon ? '/images/blog/' . get_settings()->blog_favicon : '/images/blog/default-favicon.png' ?>" alt="favicon" class="img-thumbnail" id="favicon-image-preview">
                            </div>
                            <form action="<?= route_to('admin.update-blog-favicon') ?>" method="POST" enctype="multipart/form-data" id="changeBlogFaviconForm">
                                <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" class="ci_csrf_data">
                                <div class="mb-2">
                                    <input type="file" name="blog_favicon" id="" class="form-control">
                                    <span class="text-danger error-text"></span>
                                </div>
                                <button type="submit" class="btn btn-primary">Change favicon</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="social_media" role="tabpanel">
                <div class="pd-20">
                    <form action="" method="POST" id="social_media_form">
                        <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" class="ci_csrf_data">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Facebook URL</label>
                                    <input type="text" name="facebook_url" class="form-control" placeholder="Enter facebook page url" value="<?= get_social_media()->facebook_url; ?>">
                                    <span class="text-danger error-text facebook_url_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Twitter URL</label>
                                    <input type="text" name="twitter_url" class="form-control" placeholder="Enter twitter page url" value="<?= get_social_media()->twitter_url; ?>">
                                    <span class=" text-danger error-text twitter_url_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Instagram URL</label>
                                    <input type="text" name="instagram_url" class="form-control" placeholder="Enter instagram page url" value="<?= get_social_media()->instagram_url; ?>">
                                    <span class=" text-danger error-text instagram_url_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Youtube URL</label>
                                    <input type="text" name="youtube_url" class="form-control" placeholder="Enter youtube page url" value="<?= get_social_media()->youtube_url; ?>">
                                    <span class=" text-danger error-text youtube_url_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Github URL</label>
                                    <input type="text" name="github_url" class="form-control" placeholder="Enter github page url" value="<?= get_social_media()->github_url; ?>">
                                    <span class=" text-danger error-text github_url_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Linkedin URL</label>
                                    <input type="text" name="linkedin_url" class="form-control" placeholder="Enter linkedin page url" value="<?= get_social_media()->linkedin_url; ?>">
                                    <span class=" text-danger error-text linkedin_url_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $('#general_settings_form').on('submit', function(e) {
        e.preventDefault();
        // CSRF HASH
        var csrfName = $('.ci_csrf_data').attr('name');
        var csrfHash = $('.ci_csrf_data').val();
        var form = this;
        var formdata = new FormData(form);
        formdata.append(csrfName, csrfHash);

        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formdata,
            processData: false,
            dataType: 'json',
            contentType: false,
            cache: false,
            beforeSend: function() {
                toastr.remove();
                $(form).find('span.error-text').text('');
            },
            success: function(response) {
                // update csrf hash
                $('.ci_csrf_data').val(response.token);
                if ($.isEmptyObject(response.errors)) {
                    if (response.status == 1) {
                        toastr.success(response.msg);
                    } else {
                        toastr.error(response.msg);
                    }
                } else {
                    $.each(response.errors, function(prefix, val) {
                        $(form).find('span.' + prefix + '_error').text(val);
                    })
                }
            }
        })
    });

    $('#changeBlogLogoForm').on('submit', function(e) {
        e.preventDefault();
        var csrfName = $('.ci_csrf_data').attr('name');
        var csrfHash = $('.ci_csrf_data').val();
        var form = this;
        var formdata = new FormData(form);
        formdata.append(csrfName, csrfHash);
        var inputFileVal = $(form).find('input[type="file"][name="blog_logo"]').val();

        if (inputFileVal.length > 0) {
            $.ajax({
                url: $(form).attr('action'),
                method: $(form).attr('method'),
                data: formdata,
                processData: false,
                dataType: 'json',
                contentType: false,
                beforeSend: function() {
                    toastr.remove();
                    $(form).find('span.error-text').text('');
                },
                success: function(response) {
                    // update csrf hash
                    $('.ci_csrf_data').val(response.token);
                    if (response.status == 1) {
                        localStorage.setItem('toastrMessage', response.msg);
                        localStorage.setItem('toastrType', 'success');
                        location.reload();
                    } else {
                        toastr.error(response.msg);
                    }
                }
            })
        } else {
            $(form).find('span.error-text').text('Please select logo image file. PNG file type is recommended.');
        }
    })
    $(document).ready(function() {
        const toastrMessage = localStorage.getItem('toastrMessage');
        const toastrType = localStorage.getItem('toastrType');
        if (toastrType === 'success') {
            toastr.success(toastrMessage);
        } else if (toastrType === 'error') {
            toastr.error(toastrMessage);
        }

        localStorage.removeItem('toastrMessage');
        localStorage.removeItem('toastrType');
    })
    $('#changeBlogFaviconForm').on('submit', function(e) {
        e.preventDefault();
        var csrfName = $('.ci_csrf_data').attr('name');
        var csrfHash = $('.ci_csrf_data').val();
        var form = this;
        var formdata = new FormData(form);
        formdata.append(csrfName, csrfHash);
        var inputFileVal = $(form).find('input[type="file"][name="blog_favicon"]').val();

        if (inputFileVal.length > 0) {
            $.ajax({
                url: $(form).attr('action'),
                method: $(form).attr('method'),
                data: formdata,
                processData: false,
                dataType: 'json',
                contentType: false,
                beforeSend: function() {
                    toastr.remove();
                    $(form).find('span.error-text').text('');
                },
                success: function(response) {
                    // update csrf hash
                    $('.ci_csrf_data').val(response.token);
                    if (response.status == 1) {
                        localStorage.setItem('toastrMessage', response.msg);
                        localStorage.setItem('toastrType', 'success');
                        location.reload();
                    } else {
                        toastr.error(response.msg);
                    }
                }
            })
        } else {
            $(form).find('span.error-text').text('Please select favicon image file. PNG file type is recommended.');
        }
    })
</script>
<?= $this->endSection(); ?>