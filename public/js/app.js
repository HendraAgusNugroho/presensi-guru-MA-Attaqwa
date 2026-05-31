(function () {
    function togglePassword(btn) {
        var inputId = btn.getAttribute('data-toggle-password');
        if (!inputId) return;
        var input = document.getElementById(inputId);
        if (!input) return;

        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.setAttribute('aria-pressed', show ? 'true' : 'false');
        btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        btn.classList.toggle('is-visible', show);
    }

    function bindPasswordToggles(root) {
        (root || document).querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            if (btn.dataset.bound === '1') return;
            btn.dataset.bound = '1';

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                togglePassword(btn);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindPasswordToggles(document);
    });

    window.bindPasswordToggles = bindPasswordToggles;
})();
