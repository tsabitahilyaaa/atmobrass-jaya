document.addEventListener('DOMContentLoaded', function () {
    initScrollAnimations();
    initMobileMenu();
});

function initScrollAnimations() {
    var elements = document.querySelectorAll('.anim-scroll');
    if (!elements.length) return;
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    elements.forEach(function (el) { observer.observe(el); });
}

function initMobileMenu() {
    var btn = document.getElementById('mobile-menu-btn');
    var menu = document.getElementById('mobile-menu');
    if (btn && menu) {
        btn.addEventListener('click', function () {
            menu.classList.toggle('hidden');
        });
    }
}

function closeMobileMenu() {
    var menu = document.getElementById('mobile-menu');
    if (menu) menu.classList.add('hidden');
}