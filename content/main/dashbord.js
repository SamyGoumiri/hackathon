const userName = localStorage.getItem("userName");
if (userName) {
  document.getElementById("welcome").textContent = `Welcome "${userName}"`;
  document.title = `Welcome "${userName}"`;
}

document.querySelectorAll('.languages button').forEach(button => {
    button.addEventListener('click', () => {
      const lang = button.getAttribute('data-lang');
      alert(`Langue choisie : ${button.textContent}`);
      
      // Rediriger vers une autre page ou stocker le choix
      // window.location.href = `page_${lang}.html`; // Exemple
      // localStorage.setItem("lang", lang);
    });
  });
  