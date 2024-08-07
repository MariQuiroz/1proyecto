
            <!--MAIN NAVIGATION-->
            <!--===================================================-->
            <nav id="mainnav-container">
                <div id="mainnav">


                    <!--OPTIONAL : ADD YOUR LOGO TO THE NAVIGATION-->
                    <!--It will only appear on small screen devices.-->
                    <!--================================
                    <div class="mainnav-brand">
                        <a href="index.html" class="brand">
                            <img src="img/logo.png" alt="Nifty Logo" class="brand-icon">
                            <span class="brand-text">Nifty</span>
                        </a>
                        <a href="#" class="mainnav-toggle"><i class="pci-cross pci-circle icon-lg"></i></a>
                    </div>
                    -->



                    <!--Menu-->
                    <!--================================-->
                    <div id="mainnav-menu-wrap">
                        <div class="nano">
                            <div class="nano-content">

                                <!--Profile Widget-->
                                <!--================================-->
                                <div id="mainnav-profile" class="mainnav-profile">
                                    <div class="profile-wrap text-center">
                                        <div class="pad-btm">
                                            <img class="img-circle img-md" src="<?php echo base_url(); ?>adminNifty\pages\assets\img\profile-photos\1.png" alt="Profile Picture">
                                        </div>
                                        <a href="#profile-nav" class="box-block" data-toggle="collapse" aria-expanded="false">
                                            <span class="pull-right dropdown-toggle">
                                                <i class="dropdown-caret"></i>
                                            </span>
                                            <p class="mnp-name">Aaron Chavez</p>
                                            <span class="mnp-desc">aaron.cha@themeon.net</span>
                                        </a>
                                    </div>
                                    <div id="profile-nav" class="collapse list-group bg-trans">
                                        <a href="#" class="list-group-item">
                                            <i class="demo-pli-male icon-lg icon-fw"></i> View Profile
                                        </a>
                                        <a href="#" class="list-group-item">
                                            <i class="demo-pli-gear icon-lg icon-fw"></i> Settings
                                        </a>
                                        <a href="#" class="list-group-item">
                                            <i class="demo-pli-information icon-lg icon-fw"></i> Help
                                        </a>
                                        <a href="#" class="list-group-item">
                                            <i class="demo-pli-unlock icon-lg icon-fw"></i> Logout
                                        </a>
                                    </div>
                                </div>


                                <!--Shortcut buttons-->
                                <!--================================-->
                                <div id="mainnav-shortcut" class="hidden">
                                    <ul class="list-unstyled shortcut-wrap">
                                        <li class="col-xs-3" data-content="My Profile">
                                            <a class="shortcut-grid" href="#">
                                                <div class="icon-wrap icon-wrap-sm icon-circle bg-mint">
                                                <i class="demo-pli-male"></i>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="col-xs-3" data-content="Messages">
                                            <a class="shortcut-grid" href="#">
                                                <div class="icon-wrap icon-wrap-sm icon-circle bg-warning">
                                                <i class="demo-pli-speech-bubble-3"></i>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="col-xs-3" data-content="Activity">
                                            <a class="shortcut-grid" href="#">
                                                <div class="icon-wrap icon-wrap-sm icon-circle bg-success">
                                                <i class="demo-pli-thunder"></i>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="col-xs-3" data-content="Lock Screen">
                                            <a class="shortcut-grid" href="#">
                                                <div class="icon-wrap icon-wrap-sm icon-circle bg-purple">
                                                <i class="demo-pli-lock-2"></i>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <!--================================-->
                                <!--End shortcut buttons-->


                                <ul id="mainnav-menu" class="list-group">
						
						            <!--Category name-->
						            <li class="list-header">Navigation</li>
						
						            <!--Menu list item-->
						            <li class="active-sub">
						                <a href="#">
						                    <i class="demo-pli-home"></i>
						                    <span class="menu-title">Dashboard</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse in">
						                    <li class="active-link"><a href="<?php echo base_url(); ?>adminNifty\pages\index.php">Dashboard 1</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\dashboard-2.php">Dashboard 2</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\dashboard-3.php">Dashboard 3</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-split-vertical-2"></i>
						                    <span class="menu-title">Layouts</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-collapsed-navigation.php">Collapsed Navigation</a></li>
                                            <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-offcanvas-navigation.php">Off-Canvas Navigation</a></li>
                                            <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-offcanvas-slide-in-navigation.php">Slide-in Navigation</a></li>
                                            <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-offcanvas-revealing-navigation.php">Revealing Navigation</a></li>
                                            <li class="list-divider"></li>
                                            <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-aside-right-side.php">Aside on the right side</a></li>
                                            <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-aside-left-side.php">Aside on the left side</a></li>
                                            <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-aside-dark-theme.php">Dark version of aside</a></li>
                                            <li class="list-divider"></li>
                                            <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-fixed-navbar.php">Fixed Navbar</a></li>
                                            <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-fixed-footer.php">Fixed Footer</a></li>
                                            
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="<?php echo base_url(); ?>adminNifty\pages\widgets.php">
						                    <i class="demo-pli-gear"></i>
						                    <span class="menu-title">
												Widgets
												<span class="pull-right badge badge-warning">24</span>
											</span>
						                </a>
						            </li>
						
						            <li class="list-divider"></li>
						
						            <!--Category name-->
						            <li class="list-header">Components</li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-boot-2"></i>
						                    <span class="menu-title">UI Elements</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-buttons.php">Buttons</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-panels.php">Panels</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-modals.php">Modals</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-progress-bars.php">Progress bars</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-components.php">Components</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-typography.php">Typography</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-list-group.php">List Group</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-tabs-accordions.php">Tabs &amp; Accordions</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-alerts-tooltips.php">Alerts &amp; Tooltips</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-pen-5"></i>
						                    <span class="menu-title">Forms</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-general.php">General</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-components.php">Advanced Components</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-validation.php">Validation</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-wizard.php">Wizard</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-file-upload.php">File Upload</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-text-editor.php">Text Editor</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-markdown.php">Markdown</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-receipt-4"></i>
						                    <span class="menu-title">Tables</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\tables-static.php">Static Tables</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\tables-bootstrap.php">Bootstrap Tables</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\tables-datatable.php">Data Tables</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\tables-footable.php">Foo Tables</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-bar-chart"></i>
						                    <span class="menu-title">Charts</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\charts-morris-js.php">Morris JS</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\charts-flot-charts.php">Flot Charts</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\charts-easy-pie-charts.php">Easy Pie Charts</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\charts-sparklines.php">Sparklines</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-repair"></i>
						                    <span class="menu-title">Miscellaneous</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-timeline.php">Timeline</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-maps.php">Google Maps</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\xplugins-notifications.php">Notifications<span class="label label-purple pull-right">Improved</span></a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-nestable-list.php">Nestable List</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-animate-css.php">CSS Animations</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-css-loaders.php">CSS Loaders</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-spinkit.php">Spinkit</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-tree-view.php">Tree View</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-clipboard.php">Clipboard</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-x-editable.php">X-Editable</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-warning-window"></i>
						                    <span class="menu-title">Grid System</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\grid-bootstrap.php">Bootstrap Grid</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\grid-liquid-fixed.php">Liquid Fixed</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\grid-match-height.php">Match Height</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\grid-masonry.php">Masonry</a></li>
                                
						                </ul>
						            </li>
						
						            <li class="list-divider"></li>
						
						            <!--Category name-->
						            <li class="list-header">More</li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-computer-secure"></i>
						                    <span class="menu-title">App Views</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-file-manager.php">File Manager</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-users.php">Users</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-users-2.php">Users 2</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-profile.php">Profile</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-calendar.php">Calendar</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-taskboard.php">Taskboard</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-chat.php">Chat</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-contact-us.php">Contact Us</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-speech-bubble-5"></i>
						                    <span class="menu-title">Blog Apps</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog.php">Blog</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-list.php">Blog List</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-list-2.php">Blog List 2</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-details.php">Blog Details</a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-manage-posts.php">Manage Posts</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-add-edit-post.php">Add Edit Post</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-mail"></i>
						                    <span class="menu-title">Email</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\mailbox.php">Inbox</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\mailbox-message.php">View Message</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\mailbox-compose.php">Compose Message</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\mailbox-templates.php">Email Templates</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="<?php echo base_url(); ?>adminNifty\pages\demo-pli-file-html"></i>
						                    <span class="menu-title">Other Pages</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-blank.php">Blank Page</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-invoice.php">Invoice</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-search-results.php">Search Results</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-faq.php">FAQ</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-pricing.php">Pricing<span class="label label-success pull-right">New</span></a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-404-alt.php">Error 404 alt</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-500-alt.php">Error 500 alt</a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-404.php">Error 404 </a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-500.php">Error 500</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-maintenance.php">Maintenance</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-login.php">Login</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-register.php">Register</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-password-reminder.php">Password Reminder</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-lock-screen.php">Lock Screen</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-photo-2"></i>
						                    <span class="menu-title">Gallery</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-columns.php">Columns</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-justified.php">Justified</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-nested.php">Nested</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-grid.php">Grid</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-carousel.php">Carousel</a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-slider.php">Slider</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-default-theme.php">Default Theme</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-compact-theme.php">Compact Theme</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-grid-theme.php">Grid Theme</a></li>
                                
						                </ul>
						            </li>


                                    <!--Menu list item-->
                                    <li>
                                        <a href="#">
                                            <i class="demo-pli-tactic"></i>
                                            <span class="menu-title">Menu Level</span>
                                            <i class="arrow"></i>
                                        </a>

                                        <!--Submenu-->
                                        <ul class="collapse">
                                            <li><a href="#">Second Level Item</a></li>
                                            <li><a href="#">Second Level Item</a></li>
                                            <li><a href="#">Second Level Item</a></li>
                                            <li class="list-divider"></li>
                                            <li>
                                                <a href="#">Third Level<i class="arrow"></i></a>

                                                <!--Submenu-->
                                                <ul class="collapse">
                                                    <li><a href="#">Third Level Item</a></li>
                                                    <li><a href="#">Third Level Item</a></li>
                                                    <li><a href="#">Third Level Item</a></li>
                                                    <li><a href="#">Third Level Item</a></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="#">Third Level<i class="arrow"></i></a>

                                                <!--Submenu-->
                                                <ul class="collapse">
                                                    <li><a href="#">Third Level Item</a></li>
                                                    <li><a href="#">Third Level Item</a></li>
                                                    <li class="list-divider"></li>
                                                    <li><a href="#">Third Level Item</a></li>
                                                    <li><a href="#">Third Level Item</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>

						
						            <li class="list-divider"></li>
						
						            <!--Category name-->
						            <li class="list-header">Extras</li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-happy"></i>
						                    <span class="menu-title">Icons Pack</span>
											<i class="arrow"></i>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-ionicons.php">Ion Icons</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-themify.php">Themify</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-font-awesome.php">Font Awesome</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-flagicons.php">Flag Icon CSS</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-weather-icons.php">Weather Icons</a></li>
											
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="#">
						                    <i class="demo-pli-medal-2"></i>
						                    <span class="menu-title">
												PREMIUM ICONS
												<span class="label label-danger pull-right">BEST</span>
											</span>
						                </a>
						
						                <!--Submenu-->
						                <ul class="collapse">
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\premium-line-icons.php">Line Icons Pack</a></li>
											<li><a href="<?php echo base_url(); ?>adminNifty\pages\premium-solid-icons.php">Solid Icons Pack</a></li>
											
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="<?php echo base_url(); ?>adminNifty\pages\helper-classes.php">
						                    <i class="demo-pli-inbox-full"></i>
						                    <span class="menu-title">Helper Classes</span>
						                </a>
						            </li>                                </ul>


                                <!--Widget-->
                                <!--================================-->
                                <div class="mainnav-widget">

                                    <!-- Show the button on collapsed navigation -->
                                    <div class="show-small">
                                        <a href="#" data-toggle="menu-widget" data-target="#demo-wg-server">
                                            <i class="demo-pli-monitor-2"></i>
                                        </a>
                                    </div>

                                    <!-- Hide the content on collapsed navigation -->
                                    <div id="demo-wg-server" class="hide-small mainnav-widget-content">
                                        <ul class="list-group">
                                            <li class="list-header pad-no mar-ver">Server Status</li>
                                            <li class="mar-btm">
                                                <span class="label label-primary pull-right">15%</span>
                                                <p>CPU Usage</p>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar progress-bar-primary" style="width: 15%;">
                                                        <span class="sr-only">15%</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="mar-btm">
                                                <span class="label label-purple pull-right">75%</span>
                                                <p>Bandwidth</p>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar progress-bar-purple" style="width: 75%;">
                                                        <span class="sr-only">75%</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="pad-ver"><a href="#" class="btn btn-success btn-bock">View Details</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <!--================================-->
                                <!--End widget-->

                            </div>
                        </div>
                    </div>
                    <!--================================-->
                    <!--End menu-->

                </div>
            </nav>
            <!--===================================================-->
            <!--END MAIN NAVIGATION-->

        </div>

        