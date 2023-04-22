<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{$title ?? 'User - Dashboard'}}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{env('USER_ASSETS')}}images/logo/favicon.ico">

    <!-- page css -->
    @stack('css')

    <!-- Core css -->
    <link href="{{env('USER_ASSETS')}}css/app.min.css" rel="stylesheet">

</head>

<body>
    <div class="layout">
        <div class="vertical-layout">
            <!-- Header START -->
            <div class="header-text-dark header-nav layout-vertical header-text-light" style="background-color: rgb(90, 117, 249);">
                <div class="header-nav-wrap">
                    <div class="header-nav-left">
                        <div class="header-nav-item desktop-toggle">
                            <div class="header-nav-item-select cursor-pointer">
                                <i class="nav-icon feather icon-menu icon-arrow-right"></i>
                            </div>
                        </div>
                        <div class="header-nav-item mobile-toggle">
                            <div class="header-nav-item-select cursor-pointer">
                                <i class="nav-icon feather icon-menu icon-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="header-nav-right">
                        <div class="header-nav-item">
                            <div class="dropdown header-nav-item-select nav-notification">
                                
                            </div>
                        </div>
                       <!-- display timer start-->
                        <h1>
                            <div id="countdowntimer">
                                @if(Session::get('timer'))
                                    <span id="hours">00:</span>
                                    <span id="mins">00:</span>
                                    <span id="seconds">00</span>  
                                @endif
                            </div>
                        </h1>
                        <!-- display timer end -->
                        <div class="header-nav-item">
                            <div class="dropdown header-nav-item-select nav-profile" >
                                <div class="toggle-wrapper" id="nav-profile-dropdown" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-circle avatar-image" style="width: 35px; height: 35px; line-height: 35px;">
                                        @if (!empty(auth('front')->user()->toArray()['photo']) && file_exists(base_path()."/public/".env('USER_PROFILE_PATH').auth('front')->user()->toArray()['photo']))
                                            <img src="{{env('USER_PROFILE_PIC').auth('front')->user()->toArray()['photo']}}" alt="" />
                                        @else
                                            @if (auth('front')->user()->toArray()['salute'] == 1)
                                                <img src="{{env('USER_PROFILE_PIC')}}male.png" alt="" />
                                            @else
                                                <img src="{{env('USER_PROFILE_PIC')}}female.png" alt="" />
                                            @endif
                                        @endif
                                    </div>
                                    <span class="fw-bold mx-1">{{strtoupper(auth('front')->user()->toArray()['first_nm']." ".auth('front')->user()->toArray()['last_nm'])}}</span>
                                    <i class="feather icon-chevron-down"></i>
                                </div>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="nav-profile-header">
                                       <div class="d-flex align-items-center">
                                            <div class="avatar avatar-circle avatar-image">
                                                @if (!empty(auth('front')->user()->toArray()['photo']) && file_exists(base_path()."/public/".env('USER_PROFILE_PATH').auth('front')->user()->toArray()['photo']))
                                                    <img src="{{env('USER_PROFILE_PIC').auth('front')->user()->toArray()['photo']}}" alt="" />
                                                @else
                                                    @if (auth('front')->user()->toArray()['salute'] == 1)
                                                        <img src="{{env('USER_PROFILE_PIC')}}male.png" alt="" />
                                                    @else
                                                        <img src="{{env('USER_PROFILE_PIC')}}female.png" alt="" />
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column ms-1">
                                                <span class="fw-bold text-dark">{{strtoupper(auth('front')->user()->toArray()['first_nm']." ".auth('front')->user()->toArray()['last_nm'])}}</span>
                                                <span class="font-size-sm">{{auth('front')->user()->toArray()['emailid']}}</span>
                                            </div>
                                       </div>
                                    </div>
                                    <a href="{{route('viewMyProfile')}}" class="dropdown-item">
                                       <div class="d-flex align-items-center">
                                           <i class="font-size-lg me-2 feather icon-user"></i>
                                           <span>Profile</span>
                                        </div>
                                    </a>
                                    {{-- <a href="javascript:void(0)" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePasswordModel">
                                       <div class="d-flex align-items-center">
                                           <i class="font-size-lg me-2 feather icon-settings"></i>
                                           <span>Change Password</span>
                                        </div>
                                    </a> --}}
                                    {{-- <a href="javascript:void(0)" class="dropdown-item">
                                       <div class="d-flex align-items-center"><i class="font-size-lg me-2 feather icon-life-buoy"></i>
                                        <span>Support</span>
                                        </div>
                                    </a> --}}
                                    <a href="{{route('logout')}}" class="dropdown-item">
                                       <div class="d-flex align-items-center"><i class="font-size-lg me-2 feather icon-power"></i>
                                        <span>Sign Out</span>
                                    </div>
                                    </a>
                                 </div>
                            </div>
                        </div>     
                    </div>
                </div>
            </div>    
            <!-- Header END -->

            <!-- Side Nav START -->
            <div class="side-nav vertical-menu nav-menu-light scrollable ps-container ps-theme-default ps-active-y">
                <div class="nav-logo">
                    <div class="w-100 logo">
                        <img class="img-fluid" src="{{env('USER_ASSETS')}}images/logo/logo.png" style="max-height: 70px;padding-top: 10px;" alt="logo">
                    </div>
                    <div class="mobile-close">
                        <i class="icon-arrow-left feather"></i>
                    </div>
                 </div>
                 {{-- {{auth('front')->check()}} --}}
                 {{-- {{session()->get('load_dashboard').'.dashboard'}} --}}
                 <ul class="nav-menu">
                    @if(auth('front')->user()->can(session()->get('load_dashboard').'-dashboard'))
                        <li class="nav-menu-item {{Route::currentRouteNamed(session()->get('load_dashboard').'.dashboard') ? 'router-link-active' : ''}}" >
                            <a href="{{route(session()->get('load_dashboard').'.dashboard')}}">
                                <i class="feather icon-home"></i>
                                <span class="nav-menu-item-title">Dashboard</span>
                            </a>
                        </li>
                    @endif
                    {{-- <pre> --}}
                    {{-- {{print_r(session()->all())}} --}}
                    {{-- {{dd(Auth::guard('front')->user()->toArray())}} --}}
                    {{-- {{dd(auth('front')->user()->toArray()['first_nm'])}} --}}


                    @role(['super-admin'])
                        <li class="nav-menu-item {{Route::currentRouteNamed('system.users') ? 'router-link-active' : ''}}" >
                            <a href="{{route('admin.system.users')}}">
                                <i class="icon-users feather"></i>
                                <span class="nav-menu-item-title">System Users</span>
                            </a>
                        </li>
                    @endrole

                    @role(['super-admin'])
                        <li class="nav-group-title">ROLE</li>
                        <li class="nav-submenu {{preg_match('/\badmin.role-permission\b/', Route::currentRouteName()) ? 'open' : ''}}">
                            <a class="nav-submenu-title">
                            <i class="icon-user feather"></i>
                            <span>Role</span>
                            <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse" style="display:{{preg_match('/\badmin.role-permission\b/', Route::currentRouteName()) ? 'block' : 'none'}}">
                                <li class="nav-menu-item {{preg_match('/\badmin.role-permission.view\b/', Route::currentRouteName()) ? 'router-link-active' : ''}}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{route('admin.role-permission.view.allRole')}}">View Role</a>
                                </li>
                            </ul>
                        </li>
                    @endrole

                    <li class="nav-group-title">LEADS</li>
                    @role(['super-admin'])
                        <li class="nav-submenu {{preg_match('/\badmin.leads\b/', Route::currentRouteName()) ? 'open' : ''}}">
                            <a class="nav-submenu-title">
                            <i class="icon-user feather"></i>
                            <span>Admin</span>
                            <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse" style="display:{{preg_match('/\badmin.leads\b/', Route::currentRouteName()) ? 'block' : 'none'}}">
                                <li class="nav-menu-item {{preg_match('/\badmin.leads.add\b/', Route::currentRouteName()) ? 'router-link-active' : ''}}">
                                    <i class="icon-plus feather"></i>
                                    <a href="{{route('admin.leads.add.bulkLeadForm')}}">Add Leads</a>
                                </li>
                                <li class="nav-menu-item {{preg_match('/\badmin.leads.view\b/', Route::currentRouteName()) ? 'router-link-active' : ''}}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{route('admin.leads.view.allLeads')}}">View all Leads</a>
                                </li>
                            </ul>
                        </li>
                    @endrole
                    @role(['business-development-executive', 'business-development-associate', 'business-development-manager'])
                        <li class="nav-submenu {{preg_match('/\btelecaller.leads\b/', Route::currentRouteName()) ? 'open' : ''}}">
                            <a class="nav-submenu-title">
                                <i class="icon-headphones feather"></i>
                                <span>Telecaller Leads</span>
                                <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse" style="display:{{preg_match('/\btelecaller.leads\b/', Route::currentRouteName()) ? 'block' : 'none'}}">
                                <li class="nav-menu-item {{preg_match('/\btelecaller.leads.add\b/', Route::currentRouteName()) ? 'router-link-active' : ''}}">
                                    <i class="icon-plus feather"></i>
                                    <a href="{{route('telecaller.leads.add.sigleLeadForm')}}">Add Leads</a>
                                </li>
                                <li class="nav-menu-item {{preg_match('/\btelecaller.leads.view\b/', Route::currentRouteName()) ? 'router-link-active' : ''}}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{route('telecaller.leads.view.allLeads')}}">View all Leads</a>
                                </li>
                                <li class="nav-menu-item {{Route::currentRouteNamed('telecaller.leads.pendingLeads') ? 'router-link-active' : ''}}">
                                    <i class="icon-clock feather"></i>
                                    <a href="{{route('telecaller.leads.pendingLeads')}}">Pending Leads</a>
                                </li>
                            </ul>
                        </li>
                    @endrole
                    @role(['business-development-executive-on-field', 'business-development-associate-on-field', 'business-development-manager-on-field'])
                        <li class="nav-submenu {{preg_match('/\bmarketing.leads\b/', Route::currentRouteName()) ? 'open' : ''}}">
                            <a class="nav-submenu-title">
                                <i class="icon-command feather"></i>
                                <span>Marketing Leads </span>
                                <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse" style="display:{{preg_match('/\bmarketing.leads\b/', Route::currentRouteName()) ? 'block' : 'none'}}">
                                <li class="nav-menu-item {{preg_match('/\bmarketing.leads.view\b/', Route::currentRouteName()) ? 'router-link-active' : ''}}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{route('marketing.leads.view.allLeads')}}">View all Leads</a>
                                </li>
                                <li class="nav-menu-item {{Route::currentRouteNamed('marketing.leads.pendingLeads') ? 'router-link-active' : ''}}">
                                    <i class="icon-clock feather"></i>
                                    <a href="{{route('marketing.leads.pendingLeads')}}">Pending Leads</a>
                                </li>
                            </ul>
                        </li>
                    @endrole
                    @role(['customer-relationship-executive', 'customer-relationship-manager'])
                        <li class="nav-submenu {{preg_match('/\bcrm.leads\b/', Route::currentRouteName()) ? 'open' : ''}}">
                            <a class="nav-submenu-title">
                                <i class="icon-box feather"></i>
                                <span>CRM Leads</span>
                                <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse" style="display:{{preg_match('/\bcrm.leads\b/', Route::currentRouteName()) ? 'block' : 'none'}}">
                                <li class="nav-menu-item {{Route::currentRouteNamed('crm.leads.view.allLeads') ? 'router-link-active' : ''}}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{route('crm.leads.view.allLeads')}}">View all Leads</a>
                                </li>
                                <li class="nav-submenu {{str_contains(Route::currentRouteName(), 'crm.leads.view.pendingLeads') ? 'open' : ''}}" style="padding: 0 calc(0.01rem + 0.8rem);">
                                    <a class="nav-submenu-title">
                                        <i class="icon-clock feather"></i>
                                        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pending Leads</span>
                                        <i class="nav-submenu-arrow"></i>
                                    </a>
                                    <ul class="nav-menu menu-collapse" style="display:{{str_contains(Route::currentRouteName(), 'crm.leads.view.pendingLeads') ? 'block' : 'none'}}">
                                        <li class="nav-menu-item {{Route::currentRouteNamed('crm.leads.view.pendingLeads') ? 'router-link-active' : ''}}">
                                            <i class="icon-eye feather"></i>
                                            <a href="{{route('crm.leads.view.pendingLeads')}}">Feedback 1</a>
                                        </li>
                                        <li class="nav-menu-item {{Route::currentRouteNamed('crm.leads.view.pendingLeadsFeedback2') ? 'router-link-active' : ''}}">
                                            <i class="icon-eye feather"></i>
                                            <a href="{{route('crm.leads.view.pendingLeadsFeedback2')}}">Feedback 2</a>
                                        </li>
                                        <li class="nav-menu-item {{Route::currentRouteNamed('crm.leads.view.pendingLeadsFeedback3') ? 'router-link-active' : ''}}">
                                            <i class="icon-eye feather"></i>
                                            <a href="{{route('crm.leads.view.pendingLeadsFeedback3')}}">Feedback 3</a>
                                        </li>
                                        <li class="nav-menu-item {{Route::currentRouteNamed('crm.leads.view.pendingLeadsNotIn') ? 'router-link-active' : ''}}">
                                            <i class="icon-eye feather"></i>
                                            <a href="{{route('crm.leads.view.pendingLeadsNotIn')}}">Not Intersted</a>
                                        </li>
                                    </ul>
                                </li>
                                
                            </ul>
                        </li>
                    @endrole
                    <li class="nav-group-title">AREA</li>
                    <li class="nav-submenu {{str_contains(Route::currentRouteName(), 'area') ? 'open' : ''}}">
                        <a class="nav-submenu-title">
                            <i class="icon-map-pin feather"></i>
                            <span>Area Master</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse" style="display:{{str_contains(Route::currentRouteName(), 'area') ? 'block' : 'none'}}">
                            @if (auth('front')->user()->can('area-master-add'))
                                <li class="nav-menu-item {{Route::currentRouteNamed('area.addArea') ? 'router-link-active' : ''}}">
                                    <i class="icon-plus feather"></i>
                                    <a href="{{route('area.addArea')}}">Add Area</a>
                                </li>
                            @endif
                            @if (auth('front')->user()->can('area-master-view'))
                                <li class="nav-menu-item {{Route::currentRouteNamed('area.viewArea') ? 'router-link-active' : ''}}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{route('area.viewArea')}}">View Area</a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    <li class="nav-group-title">TERRITORY</li>
                    <li class="nav-submenu {{str_contains(Route::currentRouteName(), 'territory') ? 'open' : ''}}">
                        <a class="nav-submenu-title">
                            <i class="icon-map-pin feather"></i>
                            <span>Territory Master</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse" style="display:{{str_contains(Route::currentRouteName(), 'territory') ? 'block' : 'none'}}">
                            @if (auth('front')->user()->can('territory-master-add'))
                                <li class="nav-menu-item {{Route::currentRouteNamed('territory.addTerritory') ? 'router-link-active' : ''}}">
                                    <i class="icon-plus feather"></i>
                                    <a href="{{route('territory.addTerritory')}}">Add Territory</a>
                                </li>
                            @endif    
                            @if (auth('front')->user()->can('territory-master-view'))
                                <li class="nav-menu-item {{Route::currentRouteNamed('territory.viewTerritory') ? 'router-link-active' : ''}}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{route('territory.viewTerritory')}}">View Territory</a>
                                </li>
                            @endif    
                        </ul>
                    </li>
                    {{-- <li class="nav-menu-item">
                        <a href="v-calendar.html">
                            <i class="feather icon-calendar"></i>
                            <span class="nav-menu-item-title">Calendar</span>
                        </a>
                    </li> --}}
                    {{--<li class="nav-group-title">USER INTERFACE</li>
                    <li class="nav-submenu">
                        <a class="nav-submenu-title">
                            <i class="feather icon-box"></i>
                            <span>UI Elements</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse">
                            <li class="nav-menu-item">
                                <a href="v-avatar.html">Avatar</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-alert.html">Alert</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-badge.html">Badge</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-buttons.html">Buttons</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-cards.html">Cards</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-icons.html">Icons</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-lists.html">Lists</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-typography.html">Typography</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-submenu">
                        <a class="nav-submenu-title">
                            <i class="feather icon-package"></i>
                            <span>Components</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse">
                            <li class="nav-menu-item">
                                <a href="v-accordion.html">Accordion</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-carousel.html">Carousel</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-dropdown.html">Dropdown</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-modals.html">Modals</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-toasts.html">Toasts</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-popover.html">Popover</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-progress.html">Progress</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-tabs.html">Tabs</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-tooltips.html">Tooltips</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-submenu">
                        <a class="nav-submenu-title">
                            <i class="feather icon-file-text"></i>
                            <span>Forms</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse">
                            <li class="nav-menu-item">
                                <a href="v-form-elements.html">Form Elements</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-form-layouts.html">Form Layouts</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-form-validation.html">Form Validation</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-submenu">
                        <a class="nav-submenu-title">
                            <i class="feather icon-grid"></i>
                            <span>Tables</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse">
                            <li class="nav-menu-item">
                                <a href="v-basic-table.html">Basic Table</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-data-table.html">Data Table</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-menu-item">
                        <a href="v-chart.html">
                            <i class="feather icon-bar-chart"></i>
                            <span class="nav-menu-item-title">Chart</span>
                        </a>
                    </li>
                    <li class="nav-group-title">PAGES</li>
                    <li class="nav-submenu">
                        <a class="nav-submenu-title">
                            <i class="feather icon-settings"></i>
                            <span>Utility</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse">
                            <li class="nav-menu-item">
                                <a href="v-profile-personal.html">Profile</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-invoice.html">Invoice</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-faq.html">FAQ</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-pricing.html">Pricing</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="v-user-list.html">User List</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-submenu">
                        <a class="nav-submenu-title">
                            <i class="feather icon-lock"></i>
                            <span>Auth</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse">
                            <li class="nav-menu-item">
                                <a href="login.html">Login</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="login-v2.html">Login v2</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="login-v3.html">Login v3</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="register.html">Register</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="register-v2.html">Register v2</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="register-v3.html">Register v3</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-submenu">
                        <a class="nav-submenu-title">
                            <i class="feather icon-slash"></i>
                            <span>Errors</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse">
                            <li class="nav-menu-item">
                                <a href="error.html">Error 1</a>
                            </li>
                            <li class="nav-menu-item">
                                <a href="error-v2.html">Error 2</a>
                            </li>
                        </ul>
                    </li> --}}
                </ul>
            </div>
            <!-- Side Nav END -->