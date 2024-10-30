<?= $this->extend('frontend/layout/pages-layout'); ?>
<?= $this->section('content'); ?>

<div class="row">
    <div class="col-12">
        <div class="breadcrumbs mb-4"> <a href="/">Home</a>
            <span class="mx-1">/</span> <a href="#!">Contact</a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="pr-0 pr-lg-4">
            <div class="content">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labor.
                <div class="mt-5">
                    <p class="h3 mb-3 font-weight-normal"><a class="text-dark" href="mailto:<?= get_settings()->blog_email ?>"><?= get_settings()->blog_email; ?></a>
                    </p>
                    <p class="mb-3"><a class="text-dark" href="tel:<?= get_settings()->blog_phone ?>"><?= get_settings()->blog_phone; ?></a>
                    </p>
                    <p class="mb-2">Any address here, ST 555 Where</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mt-4 mt-lg-0">
        <?php $validation = Config\Services::validation(); ?>
        <form method="POST" action="<?= route_to('contact-us-send') ?>" class="row">
            <?= csrf_field(); ?>
            <div>
                <?php if (!empty(session()->getFlashdata('success'))): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span"></button>
                    </div>
                <?php endif; ?>
                <?php if (!empty(session()->getFlashdata('fail'))): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('fail'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span"></button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control mb-4" placeholder="Name" name="name" id="name" value="<?= set_value('name'); ?>">
                <?php if ($validation->getError('name')): ?>
                    <div class="d-block text-danger" style="margin-top: -25px; margin-bottom: 15px;">
                        <?= $validation->getError('name'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <input type="email" class="form-control mb-4" placeholder="Email" name="email" id="email" set_value="<?= set_value('email'); ?>">
                <?php if ($validation->getError('email')): ?>
                    <div class="d-block text-danger" style="margin-top: -25px; margin-bottom: 15px;">
                        <?= $validation->getError('email'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12">
                <input type="text" class="form-control mb-4" placeholder="Subject" name="subject" id="subject" value="<?= set_value('subject'); ?>">
                <?php if ($validation->getError('subject')): ?>
                    <div class="d-block text-danger" style="margin-top: -25px; margin-bottom: 15px;">
                        <?= $validation->getError('subject'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12">
                <textarea name="message" id="message" class="form-control mb-4" placeholder="Type You Message Here" rows="5"><?= set_value('message'); ?></textarea>
                <?php if ($validation->getError('message')): ?>
                    <div class="d-block text-danger" style="margin-top: -25px; margin-bottom: 15px;">
                        <?= $validation->getError('message'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12">
                <button class="btn btn-outline-primary" type="submit">Send Message</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>