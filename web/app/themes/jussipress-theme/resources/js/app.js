document.addEventListener('DOMContentLoaded', () => {
  const openBtn  = document.getElementById('menu-open');
  const closeBtn = document.getElementById('menu-close');
  const megamenu = document.getElementById('megamenu');

  if (!openBtn || !closeBtn || !megamenu) return;

  const openMenu = () => {
    megamenu.classList.remove('translate-x-full');
    megamenu.setAttribute('aria-hidden', 'false');
    openBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
    closeBtn.focus();
  };

  const closeMenu = () => {
    megamenu.classList.add('translate-x-full');
    megamenu.setAttribute('aria-hidden', 'true');
    openBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    openBtn.focus();
  };

  openBtn.addEventListener('click', openMenu);
  closeBtn.addEventListener('click', closeMenu);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && megamenu.getAttribute('aria-hidden') === 'false') {
      closeMenu();
    }
  });
});
