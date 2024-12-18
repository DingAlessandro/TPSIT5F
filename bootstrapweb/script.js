export const initializeTooltips = () => {
  [...document.querySelectorAll('[data-bs-toggle="tooltip"]')]
      .forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
};

