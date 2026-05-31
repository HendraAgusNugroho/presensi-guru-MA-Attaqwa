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

    function bindConfirmActions(root) {
        (root || document).querySelectorAll('[data-confirm]').forEach(function (el) {
            if (el.dataset.confirmBound === '1') return;
            el.dataset.confirmBound = '1';

            var eventName = el.tagName === 'FORM' ? 'submit' : 'click';
            el.addEventListener(eventName, function (e) {
                var msg = el.getAttribute('data-confirm');
                if (msg && !window.confirm(msg)) {
                    e.preventDefault();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindPasswordToggles(document);
        bindConfirmActions(document);
    });

    window.bindPasswordToggles = bindPasswordToggles;
    window.bindConfirmActions = bindConfirmActions;
})();
