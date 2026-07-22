document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
  input.addEventListener('change', () => {
    const file = input.files && input.files[0];
    const targetId = input.getAttribute('data-preview');
    const target = targetId ? document.getElementById(targetId) : null;
    if (!file || !target || !file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.addEventListener('load', () => {
      target.src = String(reader.result);
      target.hidden = false;
    });
    reader.readAsDataURL(file);
  });
});

document.querySelectorAll('[data-auto-slug]').forEach((field) => {
  const target = document.getElementById(field.getAttribute('data-auto-slug'));
  if (!target) return;
  let manuallyEdited = target.value !== '';
  target.addEventListener('input', () => { manuallyEdited = target.value !== ''; });
  field.addEventListener('input', () => {
    if (manuallyEdited) return;
    target.value = field.value
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 70);
  });
});
