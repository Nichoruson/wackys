const tabs = document.querySelectorAll('.tab');
const cards = document.querySelectorAll('.menu-card');
const searchInput = document.getElementById('menuSearch');
const emptyState = document.getElementById('emptyState');

let activeCategory = document.querySelector('.tab.active')?.dataset.category || '';

function renderMenu() {
  const searchTerm = searchInput.value.trim().toLowerCase();
  let visibleCount = 0;

  cards.forEach((card) => {
    const matchesCategory = !activeCategory || card.dataset.category === activeCategory;
    const matchesSearch = !searchTerm || card.dataset.name.includes(searchTerm);
    const isVisible = matchesCategory && matchesSearch;

    card.style.display = isVisible ? 'flex' : 'none';

    if (isVisible) {
      visibleCount += 1;
    }
  });

  emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
}

tabs.forEach((tab) => {
  tab.addEventListener('click', () => {
    tabs.forEach((item) => item.classList.remove('active'));
    tab.classList.add('active');
    activeCategory = tab.dataset.category;
    renderMenu();
  });
});

searchInput.addEventListener('input', renderMenu);
renderMenu();
