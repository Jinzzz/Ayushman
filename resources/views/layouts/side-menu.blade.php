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
              <div class="user-pic">
                 @if(auth()->user()->profile->profile_image)
                 <img src="{{ asset('assets/uploads/doctor_profile/images/'.auth()->user()->profile->profile_image) }}" alt="user-img" class="avatar-xl rounded-circle">
                 @else
                 <img src="{{asset('assets/images/avatar.png')}}" alt="user-img" class="avatar-xl rounded-circle">
                 @endif
             </div> 
              <div class="user-info">
                 <h6 class=" mb-0 text-dark">{{ ucfirst(auth()->user()->username)}}</h6>
                 <span class="text-muted app-sidebar__user-name text-sm">Doctor</span>
             </div>
         </div>
     </div>
     <div class="sidebar-navs">
         <ul class="nav nav-pills-circle ">
             <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Logout">
                 <a href="{{route('logout')}}" class="nav-link text-center m-2" onclick="event.preventDefault();
                 document.getElementById('logout-form').submit();" >
                     <i class="fe fe-power"></i>
                       <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>

                 </a>
             </li>
         </ul>
     </div>
     <ul class="side-menu">
         <li class="slide">
             <a class="side-menu__item @yield('admin')" href="{{route('doctor.home')}}"><i class="side-menu__icon ti-home"></i><span class="side-menu__label">Dashboard</span></a>
         </li>
        <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon fa fa-user"></i><span class="side-menu__label">{{ __('Leaves') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
                      <li><a class="slide-item" href="{{route('doctor.leave.viewApplyLeave')}}">{{ __('Apply Leave') }}</a></li>
                      <li><a class="slide-item" href="{{route('doctor.leave.history')}}">{{ __('Leave History') }}</a></li>
                   
        </ul>
         </li>
          <li class="slide">
             <a class="side-menu__item @yield('admin')" href="#"><i class="side-menu__icon ti-home"></i><span class="side-menu__label">Consultations</span></a>
         </li>
           <li class="slide">
             <a class="side-menu__item @yield('admin')" href="#"><i class="side-menu__icon ti-home"></i><span class="side-menu__label">Consultation History</span></a>
         </li>
          <li class="slide">
          <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon fa fa-user"></i><span class="side-menu__label">{{ __('Profile') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
                      <li><a class="slide-item" href="{{route('doctor.profile.viewProfile')}}">{{ __('Update Profile') }}</a></li>
                      <li><a class="slide-item" href="{{route('doctor.profile.changePassword')}}">{{ __('Change Password') }}</a></li>
                      <li><a class="slide-item"  onclick="event.preventDefault();
                 document.getElementById('logout-form').submit();">{{ __('Logout') }}</a></li>

         </li>
         
     </ul>
 </aside>