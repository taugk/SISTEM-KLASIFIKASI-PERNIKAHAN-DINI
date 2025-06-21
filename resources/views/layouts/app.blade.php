<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>SISTEM INFORMASI KLASIFIKASI PERNIKAHAN DINI (SIKADIN)</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="{{asset("assets/Logo/logo.ico")}}"
      type="image/x-icon"
    />

    <!-- Fonts and icons -->
    <script src="{{ asset("assets/js/plugin/webfont/webfont.min.js") }}"></script>
    <script>
        WebFont.load({
          google: {
            families: ["Public Sans:300,400,500,600,700"]
          },
          custom: {
            families: [
              "Font Awesome 5 Solid",
              "Font Awesome 5 Regular",
              "Font Awesome 5 Brands",
              "simple-line-icons"
            ],
            urls: ["{{ asset('assets/css/fonts.min.css') }}"]
          },
          active: function () {
            sessionStorage.fonts = true;
          }
        });
      </script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{asset("assets/css/bootstrap.min.css")}}" />
    <link rel="stylesheet" href="{{ asset("assets/css/plugins.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/css/kaiadmin.min.css") }}" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Menambahkan CSS Tagify -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.4.0/tagify.min.css" rel="stylesheet" />

    <!-- Menambahkan JS Tagify -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.4.0/tagify.min.js"></script>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

  </head>
  <body>
    <!-- Loading Spinner -->
    <div id="universal-spinner" class="loading-overlay">
      <div class="loader loader-lg"></div>
    </div>

    <style>
      .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
      }
    </style>

    <div class="wrapper">
      @include('partials.sidebar')

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="index.html" class="logo">
                <img
                  src="{{ asset("assets/img/kaiadmin/logo_light.svg") }}"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>

          @include('partials.navbar')
        <div class="container">
          @yield('content')
        </div>
      </div>
    @include('partials.footer')
    </div>




    <!-- Modal Calendar -->
<div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="calendarModalLabel">Kalender Kegiatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <div class="modal-body">
        <!-- Form Tambah Kegiatan -->
        <form id="eventForm" class="mb-3">
          <div class="row g-2 align-items-center">
            <div class="col-md-8">
              <input type="text" id="eventTitle" class="form-control" placeholder="Nama kegiatan" required />
            </div>
            <div class="col-md-4">
              <input type="date" id="eventDate" class="form-control" required />
            </div>
          </div>
          <div class="text-end mt-2">
            <button type="submit" class="btn btn-primary btn-sm">Tambah Kegiatan</button>
          </div>
        </form>

        <!-- Kalender -->
        <div id="fullcalendar-container">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>
</div>


    <!--   Core JS Files   -->
    <script src="{{ asset ("assets/js/core/jquery-3.7.1.min.js")}}"></script>
    <script src="{{ asset("assets/js/core/popper.min.js") }}"></script>
    <script src="{{ asset("assets/js/core/bootstrap.min.js") }}"></script>
    <!-- jQuery Scrollbar -->
    <script src="{{ asset("assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js") }}"></script>
    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery Sparkline -->
    <script src="{{ asset("assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js") }}"></script>
    <!-- Chart Circle -->
    <script src="{{ asset("assets/js/plugin/chart-circle/circles.min.js") }}"></script>
    <!-- Datatables -->
    <script src="{{ asset("assets/js/plugin/datatables/datatables.min.js") }}"></script>
    <!-- Bootstrap Notify -->
    <script src="{{ asset("assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js") }}"></script>
    <!-- jQuery Vector Maps -->
    <script src="{{ asset("assets/js/plugin/jsvectormap/jsvectormap.min.js") }}"></script>
    <script src="{{ asset("assets/js/plugin/jsvectormap/world.js") }}"></script>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Kaiadmin JS -->
    <script src="{{ asset("assets/js/kaiadmin.js") }}"></script>

    <script>
        $(document).ready(function () {
          $("#basic-datatables").DataTable({
            pageLength: 10,
            lengthMenu: [10]
          });

          $("#multi-filter-select").DataTable({
            pageLength: 5,
            initComplete: function () {
              this.api()
                .columns()
                .every(function () {
                  var column = this;
                  var select = $(
                    '<select class="form-select"><option value=""></option></select>'
                  )
                    .appendTo($(column.footer()).empty())
                    .on("change", function () {
                      var val = $.fn.dataTable.util.escapeRegex($(this).val());

                      column
                        .search(val ? "^" + val + "$" : "", true, false)
                        .draw();
                    });

                  column
                    .data()
                    .unique()
                    .sort()
                    .each(function (d, j) {
                      select.append(
                        '<option value="' + d + '">' + d + "</option>"
                      );
                    });
                });
            },
          });

          $("#basic-datatables_filter").css("display", "none");

          $("#basic-datatables_length").css("display", "none");

          // Add Row
          $("#add-row").DataTable({
            pageLength: 5,
          });


          var action =
            '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

          $("#addRowButton").click(function () {
            $("#add-row")
              .dataTable()
              .fnAddData([
                $("#addName").val(),
                $("#addPosition").val(),
                $("#addOffice").val(),
                action,
              ]);
            $("#addRowModal").modal("hide");
          });
        });
      </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            const form = document.getElementById('form-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault(); // Cegah link mengarah langsung

                    const deleteUrl = this.getAttribute('data-url'); // Ambil URL dari data-url

                    form.setAttribute('action', deleteUrl); // Set form action ke URL yang sesuai

                    Swal.fire({
                        title: "Apakah kamu yakin?",
                        text: "Data yang dihapus tidak bisa dikembalikan!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ya, hapus!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Kirim form jika konfirmasi diterima
                        }
                    });
                });
            });
        });
        </script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const saveButton = document.getElementById('saveChangesBtn'); // Get the save button

        saveButton.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent form submission immediately

            Swal.fire({
                title: "Apakah kamu yakin?",
                text: "Perubahan yang kamu buat akan disimpan.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, simpan perubahan!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    this.closest('form').submit();
                }
            });
        });
    });
</script>

    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        @elseif(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonText: 'Coba Lagi'
            });
        @elseif(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: '{{ session('info') }}',
                confirmButtonText: 'OK'
            });
        @elseif(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: '{{ session('warning') }}',
                confirmButtonText: 'OK'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '@foreach($errors->all() as $error){{ $error }}<br>@endforeach',
                confirmButtonText: 'OK'
            });
        @endif
    </script>

<script>
    var quill = new Quill('#deskripsi', {
      theme: 'snow',
      placeholder: 'Masukkan Deskripsi Edukasi...',
      modules: {
        toolbar: [
          [{ 'font': [] }, { 'header': '1' }, { 'header': '2' }],
          [{ 'list': 'ordered' }, { 'list': 'bullet' }],
          ['bold', 'italic', 'underline'],
          [{ 'align': [] }],
          ['link']
        ]
      }
    });

    // Menyimpan konten ke dalam input tersembunyi saat form disubmit
    $('form').submit(function() {
      $('#deskripsiInput').val(quill.root.innerHTML);
    });
  </script>


<script>
  window.onload = function () {
    const spinner = document.getElementById("universal-spinner");
    const mainContent = document.getElementById("main-content");

    // Sembunyikan spinner, tampilkan konten
    if (spinner) spinner.style.display = "none";
    if (mainContent) mainContent.style.display = "block";
  };

  // Fallback untuk jaga-jaga (kalau window.onload gagal)
  setTimeout(() => {
    const spinner = document.getElementById("universal-spinner");
    const mainContent = document.getElementById("main-content");
    if (spinner && spinner.style.display !== "none") {
      console.warn("Fallback aktif: spinner disembunyikan paksa.");
      spinner.style.display = "none";
      if (mainContent) mainContent.style.display = "block";
    }
  }, 8000);
</script>



<!-- FullCalendar Library -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const calendarBtn = document.getElementById("openCalendar");
    let calendarInstance;

    // Inisialisasi modal kalender saat tombol diklik
    if (calendarBtn) {
      calendarBtn.addEventListener("click", function (e) {
        e.preventDefault();

        const modal = new bootstrap.Modal(document.getElementById("calendarModal"));
        modal.show();

        if (!calendarInstance) {
          const calendarEl = document.getElementById("calendar");
          calendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: "dayGridMonth",
            headerToolbar: {
              left: "prev,next today",
              center: "title",
              right: ""
            },
            height: 500,
            events: []
          });
          calendarInstance.render();
        }
      });
    }

    // Tangani form tambah kegiatan
    const form = document.getElementById("eventForm");
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const title = document.getElementById("eventTitle").value.trim();
      const date = document.getElementById("eventDate").value;

      if (!title || !date || !calendarInstance) return;

      // Tambah event ke calendar
      calendarInstance.addEvent({
        title: title,
        start: date,
        allDay: true
      });

      // Reset form dan beri notifikasi opsional
      form.reset();
    });
  });
</script>







    @stack('scripts')

    <script>
      // Loading spinner functions
      window.showLoading = function() {
        document.getElementById('universal-spinner').style.display = 'flex';
      };

      window.hideLoading = function() {
        document.getElementById('universal-spinner').style.display = 'none';
      };

      // Hide loading spinner when page is fully loaded
      window.addEventListener('load', function() {
        hideLoading();
      });

      // Show loading spinner before page unload
      window.addEventListener('beforeunload', function() {
        showLoading();
      });

      // Add loading spinner to all form submissions
      document.addEventListener('submit', function(e) {
        showLoading();
      });

      // Add loading spinner to all AJAX requests
      $(document).ajaxStart(function() {
        showLoading();
      });

      $(document).ajaxStop(function() {
        hideLoading();
      });
    </script>

  </body>
</html>
