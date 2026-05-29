// Presensi Guru — At-Taqwa
// Global helpers

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity .5s';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Update jam di topbar setiap menit
    const jamEl = document.querySelector('.topbar-right span:last-child');
    if (jamEl) {
        setInterval(() => {
            const now = new Date();
            const jam = now.getHours().toString().padStart(2,'0')
                      + ':' + now.getMinutes().toString().padStart(2,'0');
            jamEl.textContent = jam;
        }, 30000);
    }
});
