document.querySelectorAll('.languages button').forEach(btn => {
    btn.addEventListener('click', () => {
      const lang = btn.getAttribute('data-lang');
      alert(`You chose: ${lang}`);
      // Here, redirect or load translations
      // window.location.href = `home_${lang}.html`;
    });
  });
  