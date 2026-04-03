document.querySelectorAll('.nav-item').forEach(item => {
  item.addEventListener('click', function (e) {
    const sectionId = this.getAttribute('data-section');
    if (!sectionId) return;

    // Remove active class from all nav items
    document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));

    // Add active class to current nav item
    this.classList.add('active');

    // Hide all sections
    document.querySelectorAll('.admin-section').forEach(section => {
      section.style.display = 'none';
    });

    // Show targeted section
    const targetSection = document.getElementById('section-' + sectionId);
    if (targetSection) {
      targetSection.style.display = 'block';
    }

    // Update Topbar Title
    const titleEle = document.getElementById('current-section-title');
    if (titleEle) {
        titleEle.innerText = this.querySelector('span').innerText;
    }
  });
});
