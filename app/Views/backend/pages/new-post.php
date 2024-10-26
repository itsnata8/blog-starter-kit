<?= $this->extend('backend/layout/pages-layout'); ?>
<?= $this->section('content'); ?>

<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Add post</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('admin.home') ?>">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Add post
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-right">
            <a href="<?= route_to('admin.all-posts') ?>" class="btn btn-primary">View all posts</a>
        </div>
    </div>
</div>


<form action="<?= route_to('admin.create-post') ?>" method="POST" autocomplete="off" enctype="multipart/form-data" id="addPostForm">
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" class="ci_csrf_data">
    <div class="row">
        <div class="col-md-9">
            <div class="card card-box mb-2">
                <div class="card-body">
                    <div class="form-group">
                        <label for=""><b>Post title</b></label>
                        <input type="text" name="title" id="" class="form-control" placeholder="Enter post title">
                        <span class="text-danger error-text title_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Content</label>
                        <textarea name="content" class="form-control" id="content" cols="30" rows="10" placeholder="Type..."></textarea>
                        <span class="text-danger error-text content_error"></span>
                    </div>
                </div>
            </div>
            <div class="card card-box mb-2">
                <div class="card-header weight-500">SEO</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for=""><b>Post meta keywords</b><small>(separated by comma)</small></label>
                        <input type="text" name="meta_keywords" id="" class="form-control" placeholder="Enter post meta keywords">
                    </div>
                    <div class="form-group">
                        <label for=""><b>Post meta description</b></label>
                        <textarea name="meta_description" class="form-control" cols="30" rows="10" id="" placeholder="Enter post meta description"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-box mb-2">
                <div class="card-body">
                    <div class="form-group">
                        <label for=""><b>Post category</b></label>
                        <select name="category" id="" class="custom-select form-control">
                            <option value="">Choose...</option>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category->id; ?>"><?= $category->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="text-danger error-text category_error"></span>
                    </div>
                    <div class="form-group">
                        <label for=""><b>Post featured image</b></label>
                        <input type="file" name="featured_image" id="featured_image" class="form-control-file form-control" height="auto">
                        <span class="text-danger error-text featured_image_error"></span>
                    </div>
                    <div class="d-block mb-3" style="max-width:250px;">
                        <img src="#" class="img-thumbnail" id="image-previewer" alt="">
                    </div>
                    <div class="form-group">
                        <label for="" class="d-block"><b>Tags</b></label>
                        <input type="text" name="tags" id="" class="form-control" placeholder="Enter tags" data-role="tagsinput">
                        <span class="text-danger error-text tags_error"></span>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for=""><b>Visibility</b"></label>
                        <div class="custom-control custom-radio mb-5">
                            <input type="radio" name="visibility" id="customRadio1" class="custom-control-input" value="1" checked>
                            <label for="customRadio1" class="custom-control-label">Public</label>
                        </div>
                        <div class="custom-control custom-radio mb-5">
                            <input type="radio" name="visibility" id="customRadio2" class="custom-control-input" value="0">
                            <label for="customRadio2" class="custom-control-label">Private</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Create post</button>
    </div>
</form>

<?= $this->endSection(); ?>
<?= $this->section('stylesheets'); ?>
<link rel="stylesheet" href="/backend/src/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css">
<style>
    .cke_notifications_area {
        display: none !important;
    }
</style>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script src="/backend/src/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script src="/extra-assets/ckeditor/ckeditor.js"></script>
<script>
    $(function() {
        CKEDITOR.replace('content');
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-previewer').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }

    }
    $('#featured_image').change(function() {
        readURL(this);
    })
    $('#addPostForm').on('submit', function(e) {
        e.preventDefault();
        var csrfName = $('.ci_csrf_data').attr('name');
        var csrfHash = $('.ci_csrf_data').val();
        var form = this;
        var content = CKEDITOR.instances.content.getData();
        var formdata = new FormData(form);
        formdata.append(csrfName, csrfHash);
        formdata.append('content', content);
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

                if ($.isEmptyObject(response.errors)) {
                    if (response.status == 1) {
                        $(form)[0].reset();
                        CKEDITOR.instances.content.setData('');
                        $('img#image-previewer').attr('src', '');
                        $('input[name="tags"]').tagsinput('removeAll');
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
    })
</script>
<?= $this->endSection(); ?>