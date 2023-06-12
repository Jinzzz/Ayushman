 <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
 <aside class="app-sidebar">
     <div class="side-header">
         <a class="header-brand1" href="#">
             <img src="{{asset('assets/images/ayushman-logo.jpeg')}}" class="header-brand-img desktop-logo" alt="logo">
             <img src="{{asset('assets/images/ayushman-logo.jpeg')}}" class="header-brand-img toggle-logo" alt="logo">
             <img src="{{asset('assets/images/ayushman-logo.jpeg')}}" class="header-brand-img light-logo" alt="logo">
             <img src="{{asset('assets/images/ayushman-logo.jpeg')}}" class="header-brand-img light-logo1" alt="logo">
         </a><!-- LOGO -->
         <a aria-label="Hide Sidebar" class="app-sidebar__toggle ml-auto" data-toggle="sidebar" href="#"></a><!-- sidebar-toggle-->
     </div>
     <div class="app-sidebar__user">
         <div class="dropdown user-pro-body text-center">
             <!-- <div class="user-pic">
                 @if(auth()->user()->image)
                 <img src="{{ auth()->user()->image_url }}" alt="user-img" class="avatar-xl rounded-circle">
                 @else
                 <img src="{{asset('assets/images/avatar.png')}}" alt="user-img" class="avatar-xl rounded-circle">
                 @endif
             </div> -->
             <!-- <div class="user-info">
                 <h6 class=" mb-0 text-dark">{{ ucfirst(auth()->user()->admin_name)}}</h6>
                 <span class="text-muted app-sidebar__user-name text-sm">Administrator</span>
             </div> -->
         </div>
     </div>
     <!-- <div class="sidebar-navs">
         <ul class="nav nav-pills-circle ">
             <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Logout">
                 <a href="{{route('logout')}}" class="nav-link text-center m-2">
                     <i class="fe fe-power"></i>
                 </a>
             </li>
         </ul>
     </div> -->
     <ul class="side-menu">
         <li class="slide">
             <a class="side-menu__item @yield('admin')" href="{{route('home')}}"><i class="side-menu__icon ti-home"></i><span class="side-menu__label">Dashboard</span></a>
         </li>
         
     </ul>
 </aside>