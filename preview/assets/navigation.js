const journalMenu = document.querySelector('.journal-menu');
const journalSmallScreen = window.matchMedia('(max-width: 980px)');
if (journalMenu) {
  journalMenu.open = !journalSmallScreen.matches;
  journalSmallScreen.addEventListener('change', event => {
    journalMenu.open = !event.matches;
  });
  journalMenu.addEventListener('keydown', event => {
    if (event.key === 'Escape' && journalSmallScreen.matches) {
      journalMenu.open = false;
      journalMenu.querySelector('summary').focus();
    }
  });
}
