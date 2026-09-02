(() => {
  document.querySelectorAll('[data-table-search]').forEach((input) => {
    const table = document.querySelector(input.dataset.tableSearch);
    if (!table) return;
    input.addEventListener('input', () => {
      const query = input.value.toLowerCase().trim();
      table.querySelectorAll('tbody tr').forEach((row) => {
        row.hidden = query !== '' && !(row.dataset.search || '').includes(query);
      });
    });
  });

  document.querySelectorAll('[data-submit-form]').forEach((button) => {
    button.addEventListener('click', () => button.closest('tr')?.querySelector('form')?.submit());
  });

  document.querySelectorAll('.inline-inventory input').forEach((input) => {
    input.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') {
        event.preventDefault();
        input.closest('form')?.submit();
      }
    });
  });
})();