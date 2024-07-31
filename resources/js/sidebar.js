document.addEventListener("DOMContentLoaded", function() {
  const sidebar = document.querySelector('.sidebar');
  const navbarToggle = document.querySelector('.navbar-toggle');
  const navbarUser = document.querySelector(".navbar-user");
  const navbarTitle = document.querySelector(".navbar-title");
  const menuBtn = document.querySelector('.menu-btn i');
  const dashboardContainer = document.querySelector('.main-content')
  const backdrop = document.createElement('div');
  backdrop.className = 'sidebar-backdrop';
  document.body.appendChild(backdrop);

  // Mapping untuk title berdasarkan route name
  const titles = {
    'dashboard': 'Dashboard',
    'dashboard.atlet': 'Atlet Saya',
    'dashboard.kompetisi': 'Kompetisi',
    'dashboard.daftar': 'Daftar',
    'dashboard.kompetisi': 'Kompetisi Saya',
    'dashboard.tagihan': 'Tagihan',
    'dashboard.lunas': 'Daftar Pembayaran',
    'dashboard.bukuacara': 'Buku Acara',
    'dashboard.bukuhasil': 'Buku Hasil',
    // Tambahkan mapping lain sesuai dengan nama route Anda
  };

  function updateNavbarTitle() {
    const pageTitle = navbarTitle.getAttribute('data-page-title');
    navbarTitle.textContent = titles[pageTitle] || 'Default Title';
  }

  function toggleSidebar() {
    sidebar.classList.toggle('active');
    backdrop.classList.toggle('active');
    navbarUser.classList.toggle('hide', sidebar.classList.contains('active'));

    // Sembunyikan atau tampilkan navbar-title
    navbarTitle.classList.toggle('hide', sidebar.classList.contains('active'));

    // Ganti ikon pada menu-btn
    if (sidebar.classList.contains('active')) {
      menuBtn.classList.add('ph-caret-right');
    } else {
      menuBtn.classList.remove('ph-caret-right');
      menuBtn.classList.add('ph-caret-left');
    }
  }

  navbarToggle.addEventListener('click', toggleSidebar);
  backdrop.addEventListener('click', toggleSidebar);

  document.querySelectorAll(".menu > ul > li").forEach(function(menuItem) {
    menuItem.addEventListener('click', function(e) {
      e.stopPropagation();
      menuItem.classList.toggle("active");
      let subMenu = menuItem.querySelector(".sub-menu");
      if (subMenu) {
        subMenu.style.display = subMenu.style.display === 'block' ? 'none' : 'block';
      }
      menuItem.parentNode.querySelectorAll('li').forEach(function(sibling) {
        if (sibling !== menuItem) {
          sibling.classList.remove("active");
          if (sibling.querySelector(".sub-menu")) {
            sibling.querySelector(".sub-menu").style.display = 'none';
          }
        }
      });
    });
  });

  document.querySelector(".menu-btn").addEventListener('click', function() {
    sidebar.classList.toggle("active");

    // Sembunyikan atau tampilkan navbar-title
    navbarTitle.classList.toggle('hide', sidebar.classList.contains('active'));
    dashboardContainer.classList.toggle('main-content_sidebar-hide', sidebar.classList.contains('active'))
    // Ganti ikon pada menu-btn
    if (sidebar.classList.contains('active')) {
      menuBtn.classList.remove('ph-caret-left');
      menuBtn.classList.add('ph-caret-right');
    } else {
      menuBtn.classList.remove('ph-caret-right');
      menuBtn.classList.add('ph-caret-left');
      navbarUser.classList.remove('hide');
    }
  });

  // Panggil updateNavbarTitle untuk mengatur title saat halaman dimuat
  updateNavbarTitle();
});
