<!-- Navbar Header -->
<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom d-flex">
  <div class="container-fluid">
    <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
      <!-- Tempat search bar jika dibutuhkan -->
    </nav>

    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
      <!-- Search Mobile -->
      <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
          <i class="fa fa-search"></i>
        </a>
        <ul class="dropdown-menu dropdown-search animated fadeIn">
          <form class="navbar-left navbar-form nav-search">
            <div class="input-group">
              <input type="text" placeholder="Search ..." class="form-control" />
            </div>
          </form>
        </ul>
      </li>

      <!-- Messages -->
      <li class="nav-item topbar-icon dropdown hidden-caret">
        <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-bs-toggle="dropdown">
          <i class="fa fa-envelope"></i>
        </a>
        <ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
          <li>
            <div class="dropdown-title d-flex justify-content-between align-items-center">
              Messages <a href="#" class="small">Mark all as read</a>
            </div>
          </li>
          <li>
            <div class="message-notif-scroll scrollbar-outer">
              <div class="notif-center">
                <!-- Loop pesan jika diperlukan -->
                <a href="#">
                  <div class="notif-img">
                    <img src="{{ asset('assets/img/jm_denis.jpg') }}" alt="Img Profile" />
                  </div>
                  <div class="notif-content">
                    <span class="subject">Jimmy Denis</span>
                    <span class="block"> How are you ? </span>
                    <span class="time">5 minutes ago</span>
                  </div>
                </a>
                <!-- Tambahan pesan lainnya... -->
              </div>
            </div>
          </li>
          <li>
            <a class="see-all" href="javascript:void(0);">See all messages <i class="fa fa-angle-right"></i></a>
          </li>
        </ul>
      </li>

      <!-- Notifications -->
      <li class="nav-item topbar-icon dropdown hidden-caret">
        <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown">
          <i class="fa fa-bell"></i>
          <span class="notification">4</span>
        </a>
        <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
          <li>
            <div class="dropdown-title">You have 4 new notifications</div>
          </li>
          <li>
            <div class="notif-scroll scrollbar-outer">
              <div class="notif-center">
                <a href="#">
                  <div class="notif-icon notif-primary"><i class="fa fa-user-plus"></i></div>
                  <div class="notif-content"><span class="block">New user registered</span><span class="time">5 minutes ago</span></div>
                </a>
                <!-- Tambahan notifikasi lainnya... -->
              </div>
            </div>
          </li>
          <li>
            <a class="see-all" href="javascript:void(0);">See all notifications <i class="fa fa-angle-right"></i></a>
          </li>
        </ul>
      </li>

      <!-- Quick Actions -->
      <li class="nav-item topbar-icon dropdown hidden-caret">
        <a class="nav-link" data-bs-toggle="dropdown" href="#"><i class="fas fa-layer-group"></i></a>
        <div class="dropdown-menu quick-actions animated fadeIn">
          <div class="quick-actions-header">
            <span class="title mb-1">Quick Actions</span>
            <span class="subtitle op-7">Shortcuts</span>
          </div>
          <div class="quick-actions-scroll scrollbar-outer">
            <div class="quick-actions-items">
              <div class="row m-0">
                <a id="openCalendar" class="col-6 col-md-4 p-0" href="#">
                  <div class="quick-actions-item">
                    <div class="avatar-item bg-danger rounded-circle"><i class="far fa-calendar-alt"></i></div>
                    <span class="text">Calendar</span>
                  </div>
                </a>
                <!-- Tambahan shortcut lainnya... -->
              </div>
            </div>
          </div>
        </div>
      </li>

      <!-- User Profile -->
      <li class="nav-item topbar-user dropdown hidden-caret">
        <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#">
          <div class="avatar-sm">
            @php
            $foto = session('pengguna.foto');
            @endphp

            @if (isset($foto) && file_exists(public_path('storage/foto/' . $foto)))
                <img src="{{ asset('storage/foto/' . $foto) }}" alt="Foto Pengguna" class="avatar-img rounded-circle">
            @else

             <svg class="avatar-img rounded w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
    </svg>
            @endif


          </div>
          <span class="profile-username">
            <span class="op-7">Hi,</span>
            <span class="fw-bold">{{ session('pengguna.nama', 'guest') }}</span>
          </span>
        </a>
        <ul class="dropdown-menu dropdown-user animated fadeIn">
          <div class="dropdown-user-scroll scrollbar-outer">
            <li>
              <div class="user-box">
                <div class="avatar-lg">
                   @php
                    $foto = session('pengguna.foto');
                    @endphp

                    @if (isset($foto) && file_exists(public_path('storage/foto/' . $foto)))
                        <img src="{{ asset('storage/foto/' . $foto) }}" alt="Foto Pengguna" class="avatar-img rounded-circle">
                    @else

                    <svg class="avatar-img rounded w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                    @endif
                </div>
                <div class="u-text">
                  <h4>{{ session('pengguna.nama', 'guest') }}</h4>
                  <p class="text-muted">{{ session('pengguna.role', 'guest') }}</p>
                  <a href="{{ route('profile')}}" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                </div>
              </div>
            </li>
            <li>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">My Profile</a>
              <a class="dropdown-item" href="#">My Balance</a>
              <a class="dropdown-item" href="#">Inbox</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">Account Setting</a>
              <div class="dropdown-divider"></div>
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
              </form>
            </li>
          </div>
        </ul>
      </li>
    </ul>
  </div>
</nav>
<!-- End Navbar -->
</div>



