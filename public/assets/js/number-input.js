// Auto-clear default "0" on focus, restore on blur if user left it empty.
// Berlaku global untuk semua <input type="number"> di sistem.
// Input disabled / readonly otomatis aman karena tidak menerima focusin.
(function () {
    document.addEventListener('focusin', function (event) {
        var el = event.target;
        if (!(el instanceof HTMLInputElement)) {
            return;
        }
        if (el.type !== 'number') {
            return;
        }
        if (el.value !== '' && Number(el.value) === 0) {
            el.dataset._originalZero = '1';
            el.value = '';
        }
    });

    document.addEventListener('focusout', function (event) {
        var el = event.target;
        if (!(el instanceof HTMLInputElement)) {
            return;
        }
        if (el.type !== 'number') {
            return;
        }
        if (el.value === '' && el.dataset._originalZero === '1') {
            el.value = '0';
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
            delete el.dataset._originalZero;
        }
    });
})();
