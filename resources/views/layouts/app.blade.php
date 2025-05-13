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

  </head>
  <body>

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
    <!--   Core JS Files   -->
    <script src="{{ asset ("assets/js/core/jquery-3.7.1.min.js")}}"></script>
    <script src="{{ asset("assets/js/core/popper.min.js") }}"></script>
    <script src="{{ asset("assets/js/core/bootstrap.min.js") }}"></script>
    <!-- jQuery Scrollbar -->
    <script src="{{ asset("assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js") }}"></script>
    <!-- Chart JS -->
    <script src="{{ asset("assets/js/plugin/chart.js/chart.min.js") }}"></script>
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









    @stack('scripts')
  </body>



</html>
