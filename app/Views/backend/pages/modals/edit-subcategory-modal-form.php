<div class="modal fade bs-example-modal-lg" id="edit-subcategory-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" action="<?= route_to('admin.update-subcategory') ?>" method="post" id="update_subcategory_form">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">
                    Large modal
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <input type="hidden" name="subcategory_id">
                <div class="form-group">
                    <label for=""><b>Parent category</b></label>
                    <select name="parent_cat" id="" class="form-control">
                        <option value="">Uncategorized</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for=""><b>Sub Category name</b></label>
                    <input type="text" name="subcategory_name" class="form-control" placeholder="Enter sub category name">
                    <span class="text-danger error-text subcategory_name_error"></span>
                </div>
                <div class="form-group">
                    <label for="">Description</label>
                    <textarea name="description" id="" class="form-control" placeholder="Type..." cols="30" rows="10"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-primary action">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>