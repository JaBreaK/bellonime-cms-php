            </main>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="<?= ASSETS_URL ?>dist/js/main.js" defer></script>
    
    <!-- Admin specific JavaScript -->
    <script>
        // Confirm delete actions
        function confirmDelete(message = 'Apakah Anda yakin ingin menghapus item ini?') {
            return confirm(message);
        }
        
        // Auto-hide flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Initialize tooltips, modals, etc.
        document.addEventListener('DOMContentLoaded', () => {
            // Add any admin-specific JavaScript here
        });
    </script>
</body>
</html>