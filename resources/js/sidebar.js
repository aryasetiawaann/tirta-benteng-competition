document.addEventListener("DOMContentLoaded", function() {
  const sidebar = document.querySelector('.sidebar');
  const navbarToggle = document.querySelector('.navbar-toggle');
  const navbarUser = document.querySelector(".navbar-user");
  const backdrop = document.createElement('div');
  backdrop.className = 'sidebar-backdrop';
  document.body.appendChild(backdrop);

  function toggleSidebar() {
    sidebar.classList.toggle('active');
    backdrop.classList.toggle('active');
    navbarUser.classList.toggle('hide');
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
  });
});
