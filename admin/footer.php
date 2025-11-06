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

// Auto-expand menu if current page is in submenu
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    if (currentPath.includes('/admin/hero') || currentPath.includes('/admin/about')) {
        const homepageMenu = document.getElementById('homepage-menu');
        const homepageArrow = document.getElementById('homepage-arrow');
        if (homepageMenu) {
            homepageMenu.style.display = 'block';
            homepageArrow.classList.add('rotated');
        }
    }
});
</script>
</body>
</html>
