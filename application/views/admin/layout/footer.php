<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

        </main>
        
        <!-- Mobile Sidebar Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Admin Footer -->
        <footer class="footer-custom py-3 border-top bg-white mt-auto text-center text-muted" style="font-size: 0.85rem;">
            <div class="container-fluid">
                <span>&copy; <?php echo date('Y'); ?> <strong>DDI Checker</strong> — Administrative Portal.</span>
            </div>
        </footer>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 Success / Error Flash Notification System -->
<?php if ($this->session->flashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: <?php echo json_encode(strip_tags($this->session->flashdata('success'))); ?>,
        timer: 3500,
        timerProgressBar: true,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#0f766e',
        background: '#ffffff',
        iconColor: '#0f766e',
        customClass: {
            popup: 'rounded-4 shadow-lg p-4',
            title: 'fw-bold fs-4 text-dark mb-1',
            htmlContainer: 'text-secondary fs-6 mb-3',
            confirmButton: 'btn btn-primary px-4 py-2 rounded-3'
        }
    });
});
</script>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: <?php echo json_encode(strip_tags($this->session->flashdata('error'))); ?>,
        showConfirmButton: true,
        confirmButtonText: 'Understood',
        confirmButtonColor: '#dc2626',
        background: '#ffffff',
        customClass: {
            popup: 'rounded-4 shadow-lg p-4',
            title: 'fw-bold fs-4 text-dark mb-1',
            htmlContainer: 'text-secondary fs-6 mb-3',
            confirmButton: 'btn btn-danger px-4 py-2 rounded-3'
        }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle   = document.getElementById('sidebarToggle');
    const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    const sidebar         = document.getElementById('appSidebar');
    const backdrop        = document.getElementById('sidebarBackdrop');

    function openSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.add('active');
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.remove('active');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (sidebar && sidebar.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeSidebar();
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function() {
            closeSidebar();
        });
    }

    // Auto-close sidebar on window resize larger than 991px
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });
});
</script>
</body>
</html>
