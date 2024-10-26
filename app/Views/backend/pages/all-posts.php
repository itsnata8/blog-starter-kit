<?= $this->extend('backend/layout/pages-layout'); ?>
<?= $this->section('content'); ?>

<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>All posts</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('admin.home') ?>">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        All posts
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-right">
            <a href="<?= route_to('admin.new-post') ?>" class="btn btn-primary">Add new post</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card card-box">
            <div class="card-header">
                <div class="clearfix">
                    <div class="pull-left">All Posts</div>
                    <div class="pull-right"></div>
                </div>
            </div>
            <div class="card-body">
                <table class="data-table table stripe hover nowrap dataTable no-footer dtr-inline collapsed" id="postsTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Featured Image</th>
                            <th scope="col">Title</th>
                            <th scope="col">Category</th>
                            <th scope="col">Visibility</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section('stylesheets'); ?>
<link rel="stylesheet" href="/backend/src/plugins/datatables/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/backend/src/plugins/datatables/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="/extra-assets/jquery-ui-1.14.0/jquery-ui.min.css">
<link rel="stylesheet" href="/extra-assets/jquery-ui-1.14.0/jquery-ui.structure.min.css">
<link rel="stylesheet" href="/extra-assets/jquery-ui-1.14.0/jquery-ui.theme.css">
<?= $this->endSection(); ?>


<?= $this->section('scripts'); ?>
<script src="/backend/src/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="/backend/src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
<script src="/backend/src/plugins/datatables/js/dataTables.responsive.min.js"></script>
<script src="/backend/src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
<script src="/extra-assets/jquery-ui-1.14.0/jquery-ui.min.js"></script>
<script>
    var posts_DT = $('table#postsTable').DataTable({
        scrollCollapse: true,
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: "<?= route_to('admin.get-posts') ?>",
        dom: "IBfrtip",
        info: true,
        fnCreatedRow: function(row, data, index) {
            $('td', row).eq(0).html(index + 1);
        },
        columnDefs: [{
            orderable: false,
            targets: [1, 3, 4, 5]
        }]
    });
    $(document).on('click', '.deletePostBtn', function(e) {
        e.preventDefault();
        var post_id = $(this).data('id');
        var url = "<?= route_to('admin.delete-post') ?>";
        swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this subcategory",
            icon: 'warning',
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.value) {
                $.getJSON(url, {
                    post_id: post_id
                }, function(response) {
                    if (response.status == 1) {
                        posts_DT.ajax.reload(null, false);
                        toastr.success(response.msg);
                    } else {
                        toastr.error(response.msg);
                    }
                })
            }
        })
    })
</script>
<?= $this->endSection(); ?>