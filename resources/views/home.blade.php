@extends('layouts.app-admin')

@section('content')
    <div class="header navbar">
        <div class="header-container">
            <div class="nav-logo">
                <router-link to="/home">
                    <b><img src="{{ asset('assets/img/logo.png') }}" alt=""></b>
                    <span class="logo">
                        <img src="{{ asset('assets/img/logo-text.png') }}" alt="">
                    </span>
                </router-link>
            </div>
            <ul class="nav-left">
                <li>
                    <a class="sidenav-fold-toggler" href="javascript:void(0);">
                        <i class="lni-menu"></i>
                    </a>
                    <a class="sidenav-expand-toggler" href="javascript:void(0);">
                        <i class="lni-menu"></i>
                    </a>
                </li>
            </ul>
            <ul class="nav-right">
                <li class="user-profile dropdown dropdown-animated scale-left">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img class="profile-img img-fluid" src="{{ asset('assets/img/avatar/avatar.jpg') }}" alt="">
                    </a>
                    <ul class="dropdown-menu dropdown-md">
                        <li>
                            <ul class="list-media">
                                <li class="list-item avatar-info">
                                    <div class="media-img">
                                        <img src="{{ asset('assets/img/avatar/avatar.jpg') }}" alt="">
                                    </div>
                                    <div class="info">
                                        <span class="title text-semibold">Tomas Murray</span>
                                        <span class="sub-title">UI/UX Desinger</span>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="lni-lock"></i>
                                <span>{{ __('Logout') }}</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <div class="side-nav expand-lg">
        <div class="side-nav-inner">
            <ul class="side-nav-menu">
                <li class="side-nav-header">
                    <span>Navigation</span>
                </li>
                <li class="nav-item dropdown open">
                    <a href="#" class="dropdown-toggle">
                        <span class="icon-holder">
                            <i class="lni-dashboard"></i>
                        </span>
                        <span class="title">Dashboard</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li class="active">
                            <a href="index.html">Dashboard 1</a>
                        </li>
                        <li>
                            <a href="index-2.html">Dashboard 2</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#">
                        <span class="icon-holder">
                            <i class="lni-cloud"></i>
                        </span>
                        <span class="title">Apps</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li>
                            <a href="email.html">Email</a>
                        </li>
                        <li>
                            <a href="calendar.html">Calendar</a>
                        </li>
                        <li>
                            <a href="chat-app.html">Chat App</a>
                        </li>
                        <li>
                            <a href="contact.html">Contact</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#">
                        <span class="icon-holder">
                            <i class="lni-layers"></i>
                        </span>
                        <span class="title">UI Elements</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li>
                            <a href="alert.html">Alert</a>
                        </li>
                        <li>
                            <a href="badge.html">Badge</a>
                        </li>
                        <li>
                            <a href="buttons.html">Buttons</a>
                        </li>
                        <li>
                            <a href="cards.html">Cards</a>
                        </li>
                        <li>
                            <a href="lists.html">Lists</a>
                        </li>
                        <li>
                            <a href="typography.html">Typography</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#">
                        <span class="icon-holder">
                            <i class="lni-timer"></i>
                        </span>
                        <span class="title">Components</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li>
                            <a href="accordion.html">Accordions</a>
                        </li>
                        <li>
                            <a href="carousel.html">Carousel</a>
                        </li>
                        <li>
                            <a href="dropdown.html">Dropdown</a>
                        </li>
                        <li>
                            <a href="modals.html">Modals</a>
                        </li>
                        <li>
                            <a href="notifications.html">Notifications</a>
                        </li>
                        <li>
                            <a href="popover.html">Popover</a>
                        </li>
                        <li>
                            <a href="slider-progress.html">Progress Bars</a>
                        </li>
                        <li>
                            <a href="tabs.html">Tabs</a>
                        </li>
                        <li>
                            <a href="tooltips.html">Tooltips</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#">
                        <span class="icon-holder">
                            <i class="lni-package"></i>
                        </span>
                        <span class="title">Icons</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li>
                            <a href="line-icons.html">Line Icons</a>
                        </li>
                        <li>
                            <a href="fontawesome-icons.html">Font Awesome</a>
                        </li>
                        <li>
                            <a href="material-icons.html">Material Design</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#">
                        <span class="icon-holder">
                            <i class="lni-files"></i>
                        </span>
                        <span class="title">Forms</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li>
                            <a href="form-elements.html">Form Elements</a>
                        </li>
                        <li>
                            <a href="form-layouts.html">Form Layouts</a>
                        </li>
                        <li>
                            <a href="form-validation.html">Form Validation</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#">
                        <span class="icon-holder">
                            <i class="lni-control-panel"></i>
                        </span>
                        <span class="title">Tables</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li>
                            <a href="basic-table.html">Basic Table</a>
                        </li>
                        <li>
                            <a href="data-table.html">Data Table</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#">
                        <span class="icon-holder">
                            <i class="lni-pie-chart"></i>
                        </span>
                        <span class="title">Charts</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li>
                            <a href="charts-morris.html">Marris Chart</a>
                        </li>
                        <li>
                            <a href="chartjs.html">ChartJs</a>
                        </li>
                        <li>
                            <a href="charts-flot.html">Flot Chart</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#">
                        <span class="icon-holder">
                            <i class="lni-map-marker"></i>
                        </span>
                        <span class="title">Map</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li>
                            <a href="google-map.html">Google Map</a>
                        </li>
                        <li>
                            <a href="vector-map.html">Vector Map</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#">
                        <span class="icon-holder">
                            <i class="lni-files"></i>
                        </span>
                        <span class="title">Pages</span>
                        <span class="arrow">
                            <i class="lni-chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu sub-down">
                        <li>
                            <a href="profile.html">Profile</a>
                        </li>
                        <li>
                            <a href="invoice.html">Invoice</a>
                        </li>
                        <li>
                            <a href="faq.html">FAQ</a>
                        </li>
                        <li>
                            <a href="login.html">Login</a>
                        </li>
                        <li>
                            <a href="sign-up.html">Sign Up</a>
                        </li>
                        <li>
                            <a href="404.html">404</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <transition name="slide-fade" mode="out-in">
        <router-view></router-view>
    </transition>
@endsection
