
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
						                    <li class="active-link"><a href="<?php echo base_url(); ?>adminNifty\pages\index.html">Dashboard 1</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\dashboard-2.html">Dashboard 2</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\dashboard-3.html">Dashboard 3</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-collapsed-navigation.html">Collapsed Navigation</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-offcanvas-navigation.html">Off-Canvas Navigation</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-offcanvas-slide-in-navigation.html">Slide-in Navigation</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-offcanvas-revealing-navigation.html">Revealing Navigation</a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-aside-right-side.html">Aside on the right side</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-aside-left-side.html">Aside on the left side</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-aside-dark-theme.html">Dark version of aside</a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-fixed-navbar.html">Fixed Navbar</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\layouts-fixed-footer.html">Fixed Footer</a></li>
                                
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="<?php echo base_url(); ?>adminNifty\pages\widgets.html">
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-buttons.html">Buttons</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-panels.html">Panels</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-modals.html">Modals</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-progress-bars.html">Progress bars</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-components.html">Components</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-typography.html">Typography</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-list-group.html">List Group</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-tabs-accordions.html">Tabs &amp; Accordions</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\ui-alerts-tooltips.html">Alerts &amp; Tooltips</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-general.html">General</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-components.html">Advanced Components</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-validation.html">Validation</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-wizard.html">Wizard</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-file-upload.html">File Upload</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-text-editor.html">Text Editor</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\forms-markdown.html">Markdown</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\tables-static.html">Static Tables</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\tables-bootstrap.html">Bootstrap Tables</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\tables-datatable.php">Data Tables</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\tables-footable.html">Foo Tables</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\charts-morris-js.html">Morris JS</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\charts-flot-charts.html">Flot Charts</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\charts-easy-pie-charts.html">Easy Pie Charts</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\charts-sparklines.html">Sparklines</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-timeline.html">Timeline</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-maps.html">Google Maps</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\xplugins-notifications.html">Notifications<span class="label label-purple pull-right">Improved</span></a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-nestable-list.html">Nestable List</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-animate-css.html">CSS Animations</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-css-loaders.html">CSS Loaders</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-spinkit.html">Spinkit</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-tree-view.html">Tree View</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-clipboard.html">Clipboard</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\misc-x-editable.html">X-Editable</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\grid-bootstrap.html">Bootstrap Grid</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\grid-liquid-fixed.html">Liquid Fixed</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\grid-match-height.html">Match Height</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\grid-masonry.html">Masonry</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-file-manager.html">File Manager</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-users.html">Users</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-users-2.html">Users 2</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-profile.html">Profile</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-calendar.html">Calendar</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-taskboard.html">Taskboard</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-chat.html">Chat</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\app-contact-us.html">Contact Us</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog.html">Blog</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-list.html">Blog List</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-list-2.html">Blog List 2</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-details.html">Blog Details</a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-manage-posts.html">Manage Posts</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\blog-add-edit-post.html">Add Edit Post</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\mailbox.html">Inbox</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\mailbox-message.html">View Message</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\mailbox-compose.html">Compose Message</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\mailbox-templates.html">Email Templates</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-blank.html">Blank Page</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-invoice.html">Invoice</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-search-results.html">Search Results</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-faq.html">FAQ</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-pricing.html">Pricing<span class="label label-success pull-right">New</span></a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-404-alt.html">Error 404 alt</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-500-alt.html">Error 500 alt</a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-404.html">Error 404 </a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-500.html">Error 500</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-maintenance.html">Maintenance</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-login.html">Login</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-register.html">Register</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-password-reminder.html">Password Reminder</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\pages-lock-screen.html">Lock Screen</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-columns.html">Columns</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-justified.html">Justified</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-nested.html">Nested</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-grid.html">Grid</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-carousel.html">Carousel</a></li>
                                <li class="list-divider"></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-slider.html">Slider</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-default-theme.html">Default Theme</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-compact-theme.html">Compact Theme</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\gallery-grid-theme.html">Grid Theme</a></li>
                                
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-ionicons.html">Ion Icons</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-themify.html">Themify</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-font-awesome.html">Font Awesome</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-flagicons.html">Flag Icon CSS</a></li>
                                <li><a href="<?php echo base_url(); ?>adminNifty\pages\icons-weather-icons.html">Weather Icons</a></li>
											
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
						                    <li><a href="<?php echo base_url(); ?>adminNifty\pages\premium-line-icons.html">Line Icons Pack</a></li>
											<li><a href="<?php echo base_url(); ?>adminNifty\pages\premium-solid-icons.html">Solid Icons Pack</a></li>
											
						                </ul>
						            </li>
						
						            <!--Menu list item-->
						            <li>
						                <a href="<?php echo base_url(); ?>adminNifty\pages\helper-classes.html">
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

        