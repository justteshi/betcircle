const animateOnScroll = () => {
  const elements = document.querySelectorAll('[data-animate]');

  if (!elements.length) {
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) {
        return;
      }

      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, {
    threshold: 0.2,
  });

  elements.forEach((element, index) => {
    element.style.setProperty('--delay', `${index * 90}ms`);
    observer.observe(element);
  });
};

const enhanceHeader = () => {
  const body = document.body;
  const header = document.querySelector('.bc-header');

  if (!header) {
    return;
  }

  const syncScrollState = () => {
    if (window.scrollY > 24) {
      body.classList.add('bc-scrolled');
      return;
    }

    body.classList.remove('bc-scrolled');
  };

  syncScrollState();
  window.addEventListener('scroll', syncScrollState, { passive: true });
};

window.addEventListener('DOMContentLoaded', () => {
  animateOnScroll();
  enhanceHeader();
});
