<div class="left-side-bar">
    <div class="brand-logo">
        <a href="index.html">
            <img src="<?= get_settings()->blog_logo ? '/images/blog/' . get_settings()->blog_logo : '/images/blog/default-logo-dark.png' ?>" alt="logo" class="dark-logo" />
            <img
                src="<?= get_settings()->blog_logo ? '/images/blog/' . get_settings()->blog_logo : '/images/blog/default-logo-dark.png' ?>"
                alt="logo"
                class="light-logo" />
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                <li>
                    <a href="<?= route_to('admin.home') ?>" class="dropdown-toggle no-arrow <?= current_route_name() == 'admin.home' ? 'active' : '' ?>">
                        <span class="micon dw dw-home"></span><span class="mtext">Home</span>
                    </a>
                </li>
                <li>
                    <a href="<?= route_to('admin.categories') ?>" class="dropdown-toggle no-arrow <?= current_route_name() == 'admin.categories' ? 'active' : '' ?>">
                        <span class="micon dw dw-list"></span><span class="mtext">Categories</span>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" <?= current_route_name() == 'admin.all-posts' || current_route_name() == 'admin.new-post' ? 'active' : '' ?>>
                        <span class="micon dw dw-newspaper"></span><span class="mtext">Posts</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?= route_to('admin.all-posts') ?>" class="<?= current_route_name() == 'admin.all-posts' ? 'active' : '' ?>">All posts</a></li>
                        <li><a href="<?= route_to('admin.new-post') ?>" class="<?= current_route_name() == 'admin.new-post' ? 'active' : '' ?>">Add new</a></li>
                    </ul>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    <div class="sidebar-small-cap">Settings</div>
                </li>
                <li>
                    <a
                        href="<?= route_to('admin.profile') ?>"
                        class="dropdown-toggle no-arrow <?= current_route_name() == 'admin.profile' ? 'active' : '' ?>">
                        <span class="micon dw dw-user"></span>
                        <span class="mtext">Profile
                    </a>
                </li>
                <li>
                    <a
                        href="<?= route_to('admin.settings') ?>"
                        class="dropdown-toggle no-arrow <?= current_route_name() == 'admin.settings' ? 'active' : '' ?>">
                        <span class="micon dw dw-settings"></span>
                        <span class="mtext">Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>