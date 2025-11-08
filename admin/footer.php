    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSubmenu(menuId) {
    const menu = document.getElementById(menuId);
    const arrow = document.getElementById(menuId.replace('-menu', '-arrow'));
    
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
        arrow.classList.add('rotated');
    } else {
        menu.style.display = 'none';
        arrow.classList.remove('rotated');
    }
}

// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }
    
    // Auto-expand menu if current page is in submenu
    const currentPath = window.location.pathname;
    if (currentPath.includes('/admin/hero') || 
        currentPath.includes('/admin/greeting') || 
        currentPath.includes('/admin/keunggulan') || 
        currentPath.includes('/admin/about')) {
        const homepageMenu = document.getElementById('homepage-menu');
        const homepageArrow = document.getElementById('homepage-arrow');
        if (homepageMenu && homepageArrow) {
            homepageMenu.style.display = 'block';
            homepageArrow.classList.add('rotated');
        }
    }
    
    // Add smooth transitions
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});
</script>
</body>
</html>
