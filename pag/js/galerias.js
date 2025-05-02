document.addEventListener("DOMContentLoaded", function () {
  // Seleciona todas as imagens da galeria
  const galeriaItems = document.querySelectorAll(".galeria-item");

  galeriaItems.forEach((item) => {
    // Adiciona o evento de clique
    item.addEventListener("click", function () {
      const url = this.getAttribute("data-url");
      // Abre em nova aba apenas se tiver URL
      if (url && url.trim() !== "") {
        window.open(url, "_blank");
      }
    });

    // Adiciona cursor pointer apenas se tiver URL
    const url = item.getAttribute("data-url");
    if (url && url.trim() !== "") {
      item.style.cursor = "pointer";
    }
  });
});
