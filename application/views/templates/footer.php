<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php if ($this->session->userdata('logged_in')): ?>
        </main>
        <!-- Sidebar backdrop for mobile -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
        <!-- Footer -->
        <footer class="footer-custom py-3 border-top bg-white mt-auto text-center text-muted">
            <div class="container-fluid">
                <span>&copy; <?php echo date('Y'); ?> DDI Checker. Clinical Decision Support System.</span>
            </div>
        </footer>
    </div>
</div>
<?php else: ?>
    </div>
<?php endif; ?>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (sidebarToggle && sidebar && backdrop) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('active');
            backdrop.classList.toggle('show');
        });

        backdrop.addEventListener('click', function() {
            sidebar.classList.remove('active');
            backdrop.classList.remove('show');
        });
    }
});
</script>
</body>
</html>
