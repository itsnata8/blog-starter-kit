<?= $this->extend('backend/layout/pages-layout'); ?>
<?= $this->section('content'); ?>

<div class="page-header">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="title">
                <h4>Profile</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Profile
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
        <div class="pd-20 card-box height-100-p">
            <div class="profile-photo">
                <a href="javascript:;" onclick="event.preventDefault(); document.getElementById('user_profile_file').click();" class="edit-avatar"><i class="fa fa-pencil"></i></a>
                <input type="file" name="user_profile_file" id="user_profile_file" class="d-none" style="opacity: 0;">
                <img src="<?= get_user()->picture == null ? '/images/users/default-avatar.png' : '/images/users/' . get_user()->picture ?>" alt="avatar" class="ci-avatar-photo avatar">
            </div>
            <h5 class="text-center h5 mb-0"><?= get_user()->name; ?></h5>
            <p class="text-center text-muted font-14">
                <?= get_user()->email; ?>
            </p>
        </div>
    </div>
    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
        <div class="card-box height-100-p overflow-hidden">
            <div class="profile-tab height-100-p">
                <div class="tab height-100-p">
                    <ul class="nav nav-tabs customtab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#personal_details" role="tab">Personal details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#change_password" role="tab">Change password</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- Timeline Tab start -->
                        <div class="tab-pane fade show active" id="personal_details" role="tabpanel">
                            <div class="pd-20">
                                <form action="<?= route_to('admin.update-personal-details'); ?>" method="POST" id="personal-details-form">
                                    <?= csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="">Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="Enter full name" value="<?= get_user()->name; ?>">
                                            <span class="text-danger error-text name_error"> </span>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="">Username</label>
                                            <input type="text" name="username" class="form-control" placeholder="Enter full username" value="<?= get_user()->username; ?>">
                                            <span class="text-danger error-text username_error"> </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Bio</label>
                                        <textarea name="bio" cols="30" rows="10" class="form-control" placeholder="Bio...."><?= get_user()->bio; ?></textarea>
                                        <span class="text-danger error-text bio_error"> </span>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Timeline Tab End -->
                        <!-- Tasks Tab start -->
                        <div class="tab-pane fade" id="change_password" role="tabpanel">
                            <div class="pd-20 profile-task-wrap">
                                <form action="<?= route_to('admin.change-password') ?>" method="POST" id="change-password-form">
                                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" class="ci_csrf_data">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Current password</label>
                                                <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
                                                <span class="text-danger error-text current_password_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">New password</label>
                                                <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
                                                <span class="text-danger error-text new_password_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Confirm new password</label>
                                                <input type="password" name="confirm_new_password" class="form-control" placeholder="Retype new password">
                                                <span class="text-danger error-text confirm_new_password_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Change password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Tasks Tab End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $('#personal-details-form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var formdata = new FormData(form);

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
                if ($.isEmptyObject(response.errors)) {
                    if (response.status == 1) {
                        $('.ci-user-name').each(function() {
                            $(this).text(response.user_info.name);
                        });
                        toastr.success(response.msg);
                    } else {
                        toastr.error(response.msg);
                    }
                } else {
                    $.each(response.errors, function(prefix, val) {
                        $(form).find('span.' + prefix + '_error').text(val);
                    });
                }

            }
        })
    });

    $('#user_profile_file').on('change', function() {
        var file = this.files[0];
        var formData = new FormData();
        formData.append('user_profile_file', file);
        $.ajax({
            url: '<?= route_to('admin.update-picture-profile') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            dataType: 'json',
            contentType: false,
            success: function(response) {
                if (response.status == 1) {
                    // Store the toastr message in localStorage
                    localStorage.setItem('toastrMessage', response.msg);
                    localStorage.setItem('toastrType', 'success');

                    // Reload the page
                    location.reload();
                } else {
                    toastr.error(response.msg);
                }
            }
        })
    })

    // change password
    $('#change-password-form').on('submit', function(e) {
        e.preventDefault();
        // CSRF Hash
        var csrfName = $('.ci_csrf_data').attr('name');
        var csrfHash = $('.ci_csrf_data').val();
        var form = this;
        var formdata = new FormData(form);

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
                        $(form)[0].reset();
                        toastr.success(response.msg);
                    } else {
                        toastr.error(response.msg);
                    }
                } else {
                    $.each(response.errors, function(prefix, val) {
                        $(form).find('span.' + prefix + '_error').text(val);
                    });
                }
            }
        })
    });
    // show toastr after page relaod
    $(document).ready(function() {
        // Check if there's a toastr message in localStorage
        const toastrMessage = localStorage.getItem('toastrMessage');
        const toastrType = localStorage.getItem('toastrType');

        if (toastrMessage) {
            // Show the toastr based on the stored type
            if (toastrType === 'success') {
                toastr.success(toastrMessage);
            } else if (toastrType === 'error') {
                toastr.error(toastrMessage);
            }

            // Clear the toastr message and type from localStorage after showing it
            localStorage.removeItem('toastrMessage');
            localStorage.removeItem('toastrType');
        }
    });
</script>
<?= $this->endSection(); ?>