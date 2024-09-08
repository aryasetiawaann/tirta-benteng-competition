document.addEventListener("DOMContentLoaded", function() {
  const sidebar = document.querySelector('.sidebar');
  const navbarToggle = document.querySelector('.navbar-toggle');
  const navbarUser = document.querySelector(".navbar-user");
  const navbarTitle = document.querySelector(".navbar-title");
  const menuBtn = document.querySelector('.menu-btn i');
  const dashboardContainer = document.querySelector('.main-content');
  const backdrop = document.querySelector('.sidebar-backdrop');

  // Mapping untuk title berdasarkan nama route
  const titles = {
    'dashboard': 'Dashboard',
    'dashboard.atlet.index': 'Atlet Saya',
    'dashboard.atlet.edit': 'Edit Atlet',
    'dashboard.track-record.index': 'Track Record',
    'dashboard.track-record.edit': 'Edit Track Record',
    'dashboard.kompetisi': 'Daftar Kompetisi',
    'dashboard.acara': 'Acara',
    'dashboard.acara.detail': 'Detail Acara',
    'dashboard.kompe-saya': 'Kompetisi Saya',
    'dashboard.kompe-saya.acara': 'Acara',
    'dashboard.kompe-saya.acara.detail': 'Detail Acara',
    'dashboard.tagihan': 'Tagihan',
    'dashboard.tagihan.riwayat': 'Riwayat Pembayaran',
    'dashboard.bukuacara': 'Buku Acara',
    'dashboard.bukuhasil': 'Buku Hasil',
    'profile.edit': 'Profil', 
  };

  // Fungsi untuk memperbarui judul navbar berdasarkan data-page-title
  function updateNavbarTitle() {
    const pageTitle = navbarTitle.getAttribute('data-page-title');

    // Ambil elemen berdasarkan id
    const competisiElement = document.getElementById('competisi');
    const acaraDetailElement = document.getElementById('acara-detail');
    const acaraElement = document.getElementById('acara');

    if (acaraElement && (pageTitle === 'dashboard.acara' || pageTitle === 'dashboard.kompe-saya.acara')) {
      const acaraName = acaraElement.getAttribute('data-acara-name');
      navbarTitle.textContent = acaraName || 'Acara';
    } 
    else if (acaraDetailElement && (pageTitle === 'dashboard.kompe-saya.acara.detail' || pageTitle === 'dashboard.acara.detail')) {
      const acaraDetail = acaraDetailElement.getAttribute('data-acara-detail');
      navbarTitle.textContent = acaraDetail || 'Nomor Lomba';
    } 
    else if (competisiElement && pageTitle === 'dashboard.kompe-saya.acara') {
      const competisiName = competisiElement.getAttribute('data-competisi-name');
      navbarTitle.textContent = competisiName || 'Acara';
    } 
    else {
      navbarTitle.textContent = titles[pageTitle] || 'Default Title';
    }
  }

  updateNavbarTitle();

  // Fungsi untuk toggle tampilan sidebar
  function toggleSidebar() {
    sidebar.classList.toggle('active');
    backdrop.classList.toggle('active');
    navbarUser.classList.toggle('hide', sidebar.classList.contains('active'));
    navbarTitle.classList.toggle('hide', sidebar.classList.contains('active'));

    // Ganti ikon pada menu-btn berdasarkan status sidebar
    if (sidebar.classList.contains('active')) {
      menuBtn.classList.remove('ph-caret-left');
      menuBtn.classList.add('ph-caret-right');
    } else {
      menuBtn.classList.remove('ph-caret-right');
      menuBtn.classList.add('ph-caret-left');
    }
  }
  navbarToggle.addEventListener('click', toggleSidebar);
  backdrop.addEventListener('click', toggleSidebar);

  // Fungsi untuk menutup sub-menu lain selain yang saat ini dipilih
  function closeOtherSubMenus(currentMenuItem) {
    document.querySelectorAll(".menu > ul > li").forEach(function(menuItem) {
      if (menuItem !== currentMenuItem) {
        menuItem.classList.remove("active");
        let subMenu = menuItem.querySelector(".sub-menu");
        if (subMenu) {
          subMenu.style.display = 'none';
        }
      }
    });
  }

  // Menambahkan event listener pada setiap item menu untuk toggle sub-menu
  document.querySelectorAll(".menu > ul > li").forEach(function(menuItem) {
    menuItem.addEventListener('click', function(e) {
      e.stopPropagation(); // Mencegah event klik diteruskan ke parent

      const isActive = menuItem.classList.contains("active");

      // Menutup semua sub-menu lainnya
      closeOtherSubMenus(menuItem);

      if (isActive) {
        // Jika menu item sudah aktif, tutup sub-menu
        menuItem.classList.remove("active");
        let subMenu = menuItem.querySelector(".sub-menu");
        if (subMenu) {
          subMenu.style.display = 'none';
        }
      } else {
        // Jika menu item tidak aktif, buka sub-menu
        menuItem.classList.add("active");
        let subMenu = menuItem.querySelector(".sub-menu");
        if (subMenu) {
          subMenu.style.display = 'block';
        }
      }
    });
  });

  // Menambahkan event listener pada menu-btn untuk toggle sidebar
  document.querySelector(".menu-btn").addEventListener('click', function() {
    sidebar.classList.toggle("active");
    navbarTitle.classList.toggle('hide', sidebar.classList.contains('active'));
    dashboardContainer.classList.toggle('main-content_sidebar-hide', sidebar.classList.contains('active'));

    // Ganti ikon pada menu-btn berdasarkan status sidebar
    if (sidebar.classList.contains('active')) {
      menuBtn.classList.remove('ph-caret-left');
      menuBtn.classList.add('ph-caret-right');
    } else {
      menuBtn.classList.remove('ph-caret-right');
      menuBtn.classList.add('ph-caret-left');
      navbarUser.classList.remove('hide');
    }
  });

  // Menambahkan event listener untuk klik di luar sidebar untuk menutup sub-menu
  document.addEventListener('click', function(e) {
    if (!sidebar.contains(e.target)) {
      document.querySelectorAll(".menu > ul > li.active").forEach(function(menuItem) {
        // Hanya hapus kelas 'active' jika klik di luar sub-menu aktif
        let subMenu = menuItem.querySelector(".sub-menu");
        if (subMenu && !subMenu.contains(e.target)) {
          menuItem.classList.remove("active");
          subMenu.style.display = 'none';
        }
      });
    }
  });
});
