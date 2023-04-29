<!DOCTYPE html>
<html lang="en">

{{-- @if(Session::get("timer"))
    <script>history.back()</script>
@endif --}}
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $title ?? 'User - Dashboard' }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ env('USER_ASSETS') }}images/logo/favicon.ico">

    <!-- page css -->
    @stack('css')

    <!-- Core css -->
    <link href="{{ env('USER_ASSETS') }}css/app.min.css" rel="stylesheet">

</head>
<body>
    <div class="layout">
        <div class="vertical-layout" @if(Session::get("timer")) style="margin-left: 0% !important" @endif>
            <!-- Header START -->
            <div class="header-text-dark header-nav layout-vertical header-text-light w-100"  style="background-color: rgb(90, 117, 249);">
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
                    <div class="header-nav-right align-items-center ">
                        <div class="header-nav-item">
                            <div class="dropdown header-nav-item-select nav-notification">

                            </div>
                        </div>
                        <!-- display timer start-->
                        <h3 class="fw-light mb-0 me-3 text-white">
                            <div id="timer">
                                @if (Session::get('timer'))
                                    @php
                                        $o1 = new DateTime(date('H:i:s'));
                                        $o2 = new DateTime(Session::get('timer'));
                                        $diff = $o1->diff($o2, true); // to make the difference to be always positive.
                                        echo $diff->format('%H:%I:%S');
                                    @endphp
                                @endif
                            </div>
                        </h3>
                        <!-- display timer end -->
                        <div class="header-nav-item">
                            <div class="dropdown header-nav-item-select nav-profile">
                                <div class="toggle-wrapper" id="nav-profile-dropdown" @if(Session::get("timer") == "") data-bs-toggle="dropdown" @endif>
                                    <div class="avatar avatar-circle avatar-image"
                                        style="width: 35px; height: 35px; line-height: 35px;">
                                        @if (
                                            !empty(auth('front')->user()->toArray()['photo']
                                            ) &&
                                                file_exists(base_path() .
                                                        '/public/' .
                                                        env('USER_PROFILE_PATH') .
                                                        auth('front')->user()->toArray()['photo']))
                                            <img src="{{ env('USER_PROFILE_PIC') .auth('front')->user()->toArray()['photo'] }}"
                                                alt="" />
                                        @else
                                            @if (auth('front')->user()->toArray()['salute'] == 1)
                                                <img src="{{ env('USER_PROFILE_PIC') }}male.png" alt="" />
                                            @else
                                                <img src="{{ env('USER_PROFILE_PIC') }}female.png" alt="" />
                                            @endif
                                        @endif
                                    </div>
                                    <span
                                        class="fw-bold mx-1">{{ strtoupper(auth('front')->user()->toArray()['first_nm'] .' ' .auth('front')->user()->toArray()['last_nm']) }}</span>
                                    <i class="feather icon-chevron-down"></i>
                                </div>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="nav-profile-header">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-circle avatar-image">
                                                @if (
                                                    !empty(auth('front')->user()->toArray()['photo']
                                                    ) &&
                                                        file_exists(base_path() .
                                                                '/public/' .
                                                                env('USER_PROFILE_PATH') .
                                                                auth('front')->user()->toArray()['photo']))
                                                    <img src="{{ env('USER_PROFILE_PIC') .auth('front')->user()->toArray()['photo'] }}"
                                                        alt="" />
                                                @else
                                                    @if (auth('front')->user()->toArray()['salute'] == 1)
                                                        <img src="{{ env('USER_PROFILE_PIC') }}male.png"
                                                            alt="" />
                                                    @else
                                                        <img src="{{ env('USER_PROFILE_PIC') }}female.png"
                                                            alt="" />
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column ms-1">
                                                <span
                                                    class="fw-bold text-dark">{{ strtoupper(auth('front')->user()->toArray()['first_nm'] .' ' .auth('front')->user()->toArray()['last_nm']) }}</span>
                                                <span
                                                    class="font-size-sm">{{ auth('front')->user()->toArray()['emailid'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('viewMyProfile') }}" class="dropdown-item">
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
                                    <a href="{{ route('logout') }}" class="dropdown-item">
                                        <div class="d-flex align-items-center"><i
                                                class="font-size-lg me-2 feather icon-power"></i>
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
            <div class="side-nav vertical-menu nav-menu-light scrollable ps-container ps-theme-default ps-active-y" @if(Session::get("timer")) style="display: none" @endif>
                <div class="nav-logo">
                    <div class="w-100 logo">
                        <img class="img-fluid" src="{{ env('USER_ASSETS') }}images/logo/logo.png"
                            style="max-height: 70px;padding-top: 10px;" alt="logo">
                    </div>
                    <div class="mobile-close">
                        <i class="icon-arrow-left feather"></i>
                    </div>
                </div>
                {{-- {{auth('front')->check()}} --}}
                {{-- {{session()->get('load_dashboard').'.dashboard'}} --}}
                <ul class="nav-menu">
                    @if (auth('front')->user()->can(session()->get('load_dashboard') . '-dashboard'))
                        <li
                            class="nav-menu-item {{ Route::currentRouteNamed(session()->get('load_dashboard') . '.dashboard') ? 'router-link-active' : '' }}">
                            <a href="{{ route(session()->get('load_dashboard') . '.dashboard') }}">
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
                        <li
                            class="nav-menu-item {{ Route::currentRouteNamed('system.users') ? 'router-link-active' : '' }}">
                            <a href="{{ route('admin.system.users') }}">
                                <i class="icon-users feather"></i>
                                <span class="nav-menu-item-title">System Users</span>
                            </a>
                        </li>
                    @endrole

                    @role(['super-admin'])
                        <li class="nav-group-title">ROLE</li>
                        <li
                            class="nav-submenu {{ preg_match('/\badmin.role-permission\b/', Route::currentRouteName()) ? 'open' : '' }}">
                            <a class="nav-submenu-title">
                                <i class="icon-user feather"></i>
                                <span>Role</span>
                                <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse"
                                style="display:{{ preg_match('/\badmin.role-permission\b/', Route::currentRouteName()) ? 'block' : 'none' }}">
                                <li
                                    class="nav-menu-item {{ preg_match('/\badmin.role-permission.view\b/', Route::currentRouteName()) ? 'router-link-active' : '' }}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{ route('admin.role-permission.view.allRole') }}">View Role</a>
                                </li>
                            </ul>
                        </li>
                    @endrole

                    <li class="nav-group-title">LEADS</li>
                    @role(['super-admin'])
                        <li
                            class="nav-submenu {{ preg_match('/\badmin.leads\b/', Route::currentRouteName()) ? 'open' : '' }}">
                            <a class="nav-submenu-title">
                                <i class="icon-user feather"></i>
                                <span>Admin</span>
                                <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse"
                                style="display:{{ preg_match('/\badmin.leads\b/', Route::currentRouteName()) ? 'block' : 'none' }}">
                                <li
                                    class="nav-menu-item {{ preg_match('/\badmin.leads.add\b/', Route::currentRouteName()) ? 'router-link-active' : '' }}">
                                    <i class="icon-plus feather"></i>
                                    <a href="{{ route('admin.leads.add.bulkLeadForm') }}">Add Leads</a>
                                </li>
                                <li
                                    class="nav-menu-item {{ preg_match('/\badmin.leads.view\b/', Route::currentRouteName()) ? 'router-link-active' : '' }}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{ route('admin.leads.view.allLeads') }}">View all Leads</a>
                                </li>
                            </ul>
                        </li>
                    @endrole
                    @role(['business-development-executive', 'business-development-associate',
                        'business-development-manager'])
                        <li
                            class="nav-submenu {{ preg_match('/\btelecaller.leads\b/', Route::currentRouteName()) ? 'open' : '' }}">
                            <a class="nav-submenu-title">
                                <i class="icon-headphones feather"></i>
                                <span>Telecaller Leads</span>
                                <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse"
                                style="display:{{ preg_match('/\btelecaller.leads\b/', Route::currentRouteName()) ? 'block' : 'none' }}">
                                <li
                                    class="nav-menu-item {{ preg_match('/\btelecaller.leads.add\b/', Route::currentRouteName()) ? 'router-link-active' : '' }}">
                                    <i class="icon-plus feather"></i>
                                    <a href="{{ route('telecaller.leads.add.sigleLeadForm') }}">Add Leads</a>
                                </li>
                                <li
                                    class="nav-menu-item {{ preg_match('/\btelecaller.leads.view\b/', Route::currentRouteName()) ? 'router-link-active' : '' }}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{ route('telecaller.leads.view.allLeads') }}">View all Leads</a>
                                </li>
                                <li
                                    class="nav-menu-item {{ Route::currentRouteNamed('telecaller.leads.pendingLeads') ? 'router-link-active' : '' }}">
                                    <i class="icon-clock feather"></i>
                                    <a href="{{ route('telecaller.leads.pendingLeads') }}">Pending Leads</a>
                                </li>
                            </ul>
                        </li>
                    @endrole
                    @role(['business-development-executive-on-field', 'business-development-associate-on-field',
                        'business-development-manager-on-field'])
                        <li
                            class="nav-submenu {{ preg_match('/\bmarketing.leads\b/', Route::currentRouteName()) ? 'open' : '' }}">
                            <a class="nav-submenu-title">
                                <i class="icon-command feather"></i>
                                <span>Marketing Leads </span>
                                <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse"
                                style="display:{{ preg_match('/\bmarketing.leads\b/', Route::currentRouteName()) ? 'block' : 'none' }}">
                                <li
                                    class="nav-menu-item {{ preg_match('/\bmarketing.leads.view\b/', Route::currentRouteName()) ? 'router-link-active' : '' }}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{ route('marketing.leads.view.allLeads') }}">View all Leads</a>
                                </li>
                                <li
                                    class="nav-menu-item {{ Route::currentRouteNamed('marketing.leads.pendingLeads') ? 'router-link-active' : '' }}">
                                    <i class="icon-clock feather"></i>
                                    <a href="{{ route('marketing.leads.pendingLeads') }}">Pending Leads</a>
                                </li>
                            </ul>
                        </li>
                    @endrole
                    @role(['customer-relationship-executive', 'customer-relationship-manager'])
                        <li
                            class="nav-submenu {{ preg_match('/\bcrm.leads\b/', Route::currentRouteName()) ? 'open' : '' }}">
                            <a class="nav-submenu-title">
                                <i class="icon-box feather"></i>
                                <span>CRM Leads</span>
                                <i class="nav-submenu-arrow"></i>
                            </a>
                            <ul class="nav-menu menu-collapse"
                                style="display:{{ preg_match('/\bcrm.leads\b/', Route::currentRouteName()) ? 'block' : 'none' }}">
                                <li
                                    class="nav-menu-item {{ Route::currentRouteNamed('crm.leads.view.allLeads') ? 'router-link-active' : '' }}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{ route('crm.leads.view.allLeads') }}">View all Leads</a>
                                </li>
                                <li class="nav-submenu {{ str_contains(Route::currentRouteName(), 'crm.leads.view.pendingLeads') ? 'open' : '' }}"
                                    style="padding: 0 calc(0.01rem + 0.8rem);">
                                    <a class="nav-submenu-title">
                                        <i class="icon-clock feather"></i>
                                        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pending Leads</span>
                                        <i class="nav-submenu-arrow"></i>
                                    </a>
                                    <ul class="nav-menu menu-collapse"
                                        style="display:{{ str_contains(Route::currentRouteName(), 'crm.leads.view.pendingLeads') ? 'block' : 'none' }}">
                                        <li
                                            class="nav-menu-item {{ Route::currentRouteNamed('crm.leads.view.pendingLeads') ? 'router-link-active' : '' }}">
                                            <i class="icon-eye feather"></i>
                                            <a href="{{ route('crm.leads.view.pendingLeads') }}">Feedback 1</a>
                                        </li>
                                        <li
                                            class="nav-menu-item {{ Route::currentRouteNamed('crm.leads.view.pendingLeadsFeedback2') ? 'router-link-active' : '' }}">
                                            <i class="icon-eye feather"></i>
                                            <a href="{{ route('crm.leads.view.pendingLeadsFeedback2') }}">Feedback 2</a>
                                        </li>
                                        <li
                                            class="nav-menu-item {{ Route::currentRouteNamed('crm.leads.view.pendingLeadsFeedback3') ? 'router-link-active' : '' }}">
                                            <i class="icon-eye feather"></i>
                                            <a href="{{ route('crm.leads.view.pendingLeadsFeedback3') }}">Feedback 3</a>
                                        </li>
                                        <li
                                            class="nav-menu-item {{ Route::currentRouteNamed('crm.leads.view.pendingLeadsRegister') ? 'router-link-active' : '' }}">
                                            <i class="icon-eye feather"></i>
                                            <a href="{{ route('crm.leads.view.pendingLeadsRegister') }}">Intersted</a>
                                        </li>
                                        <li
                                            class="nav-menu-item {{ Route::currentRouteNamed('crm.leads.view.pendingLeadsNotIn') ? 'router-link-active' : '' }}">
                                            <i class="icon-eye feather"></i>
                                            <a href="{{ route('crm.leads.view.pendingLeadsNotIn') }}">Not Intersted</a>
                                        </li>
                                    </ul>
                                </li>

                            </ul>
                        </li>
                    @endrole
                    <li class="nav-group-title">AREA</li>
                    <li class="nav-submenu {{ str_contains(Route::currentRouteName(), 'area') ? 'open' : '' }}">
                        <a class="nav-submenu-title">
                            <i class="icon-map-pin feather"></i>
                            <span>Area Master</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse"
                            style="display:{{ str_contains(Route::currentRouteName(), 'area') ? 'block' : 'none' }}">
                            @if (auth('front')->user()->can('area-master-add'))
                                <li
                                    class="nav-menu-item {{ Route::currentRouteNamed('area.addArea') ? 'router-link-active' : '' }}">
                                    <i class="icon-plus feather"></i>
                                    <a href="{{ route('area.addArea') }}">Add Area</a>
                                </li>
                            @endif
                            @if (auth('front')->user()->can('area-master-view'))
                                <li
                                    class="nav-menu-item {{ Route::currentRouteNamed('area.viewArea') ? 'router-link-active' : '' }}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{ route('area.viewArea') }}">View Area</a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    <li class="nav-group-title">TERRITORY</li>
                    <li class="nav-submenu {{ str_contains(Route::currentRouteName(), 'territory') ? 'open' : '' }}">
                        <a class="nav-submenu-title">
                            <i class="icon-map-pin feather"></i>
                            <span>Territory Master</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse"
                            style="display:{{ str_contains(Route::currentRouteName(), 'territory') ? 'block' : 'none' }}">
                            @if (auth('front')->user()->can('territory-master-add'))
                                <li
                                    class="nav-menu-item {{ Route::currentRouteNamed('territory.addTerritory') ? 'router-link-active' : '' }}">
                                    <i class="icon-plus feather"></i>
                                    <a href="{{ route('territory.addTerritory') }}">Add Territory</a>
                                </li>
                            @endif
                            @if (auth('front')->user()->can('territory-master-view'))
                                <li
                                    class="nav-menu-item {{ Route::currentRouteNamed('territory.viewTerritory') ? 'router-link-active' : '' }}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{ route('territory.viewTerritory') }}">View Territory</a>
                                </li>
                            @endif
                        </ul>
                    </li>


                    <li class="nav-group-title">FEEDBACK</li>
                    <li class="nav-submenu {{ str_contains(Route::currentRouteName(), 'feedback') ? 'open' : '' }}">
                        <a class="nav-submenu-title">
                            <i class="icon-map-pin feather"></i>
                            <span>Feedback Master</span>
                            <i class="nav-submenu-arrow"></i>
                        </a>
                        <ul class="nav-menu menu-collapse"
                            style="display:{{ str_contains(Route::currentRouteName(), 'feedback') ? 'block' : 'none' }}">
                            @if (auth('front')->user()->can('feedback-master-add'))
                                <li
                                    class="nav-menu-item {{ Route::currentRouteNamed('feedback.addFeedback') ? 'router-link-active' : '' }}">
                                    <i class="icon-plus feather"></i>
                                    <a href="{{ route('feedback.addFeedback') }}">Add CRM Feedback</a>
                                </li>
                            @endif
                            @if (auth('front')->user()->can('feedback-master-view'))
                                <li
                                    class="nav-menu-item {{ Route::currentRouteNamed('feedback.viewFeedback') ? 'router-link-active' : '' }}">
                                    <i class="icon-eye feather"></i>
                                    <a href="{{ route('feedback.viewFeedback') }}">View CRM Feedback</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </div>
            
            <!-- Side Nav END -->

            <script type="text/javascript">
                let callStartTime = '@php echo Session::get("timer"); @endphp'

                function cntDown() {
                    elem = document.getElementById('timer').innerHTML;
                    let timer = elem.split(':')
                    let h = timer[0].trim();
                    let m = timer[1].trim();
                    let s = timer[2].trim();
                    if (s < 59) {
                        s++;
                    } else {
                        s = '0';
                    }
                    if (m < 60 && s == 59) {
                        m++;
                        m = m < 10 ? "0" + m : m
                    }
                    if (m == 60) {
                        h++;
                        m = '00';
                    }

                    document.getElementById('timer').innerHTML = h + ':' + m + ':' + (s < 10 ? "0" + s : s)

                }
                if (document.getElementById('timer').innerHTML.trim() != '') {

                    setInterval(cntDown, 1000);
                }


                const tick = () => {
                    const now = new Date(callStartTime);
                    console.log(now.getHours());

                    const h = now.getHours();
                    const m = now.getMinutes();
                    const s = now.getSeconds();

                    const html = ` 
                    <span>${h}</span> :
                    <span>${m}</span> :
                    <span>${s}</span>
                    `
                    clock.innerHTML = html;
                }
            </script>
