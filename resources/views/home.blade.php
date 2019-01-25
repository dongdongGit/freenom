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
                            <router-link to="/index"><i class="fa fa-circle-o" aria-hidden="true"></i> 首页</router-link>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="page-container">
        <div class="main-content">
            <transition name="fade" mode="out-in">
                <router-view></router-view>
            </transition>
        </div>
        
        <footer class="content-footer">
            <div class="footer">
                <div class="copyright">
                    <span>Copyright © 2018 <b class="text-dark">UIdeck</b>. All Right Reserved More Templates</span>
                </div>
            </div>
        </footer>
    </div>
</div>
   
@endsection
