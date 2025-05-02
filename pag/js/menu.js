document.addEventListener("DOMContentLoaded", function () {
  // Elementos do menu
  const menuToggle = document.querySelector(".menu-toggle");
  const menuLateral = document.querySelector(".menu-lateral");
  const fecharMenu = document.querySelector(".fechar-menu");
  const menuDropdowns = document.querySelectorAll(".menu-dropdown");
  const servicosLinks = document.querySelectorAll(".servico-link");

  // Criar overlay
  const overlay = document.createElement("div");
  overlay.classList.add("menu-overlay");
  document.body.appendChild(overlay);

  // Toggle Menu Mobile
  menuToggle?.addEventListener("click", function () {
    menuToggle.classList.add("active");
    menuLateral.classList.add("active");
    overlay.classList.add("active");
    document.body.style.overflow = "hidden";
  });

  // Fechar Menu
  function closeMenu() {
    menuToggle?.classList.remove("active");
    menuLateral?.classList.remove("active");
    overlay?.classList.remove("active");
    document.body.style.overflow = "";
  }

  fecharMenu?.addEventListener("click", closeMenu);
  overlay?.addEventListener("click", closeMenu);

  // Gerenciar Dropdowns
  menuDropdowns.forEach((dropdown) => {
    const link = dropdown.querySelector("a");
    const submenu = dropdown.querySelector(".submenu, .submenu-mobile");

    link?.addEventListener("click", function (e) {
      e.preventDefault();

      if (window.innerWidth <= 768) {
        menuDropdowns.forEach((item) => {
          if (item !== dropdown) {
            item.classList.remove("active");
          }
        });
        dropdown.classList.toggle("active");
      }
    });
  });

  // Links dos Serviços
  servicosLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      const categoriaId = this.getAttribute("href").substring(1);
      const targetGrid = document.getElementById(categoriaId);

      // Remove active de todos os grids
      document.querySelectorAll(".servicos-grid").forEach((grid) => {
        grid.style.display = "none";
        grid.classList.remove("active");
      });

      // Ativa o grid selecionado
      if (targetGrid) {
        targetGrid.style.display = "grid";
        targetGrid.classList.add("active");

        // Fecha o menu mobile
        closeMenu();

        // Scroll suave até a seção
        setTimeout(() => {
          const servicosSection = document.querySelector("#servicos");
          if (servicosSection) {
            servicosSection.scrollIntoView({ behavior: "smooth" });

            // Ativa o botão correspondente na navegação
            const tipoServico = categoriaId.replace("-grid", "");
            const btn = document.querySelector(
              `[data-categoria="${tipoServico}"]`
            );
            if (btn) {
              document
                .querySelectorAll(".categoria-btn")
                .forEach((b) => b.classList.remove("active"));
              btn.classList.add("active");
            }
          }
        }, 100);
      }
    });
  });
  // Responsividade
  window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
      closeMenu();
      menuDropdowns.forEach((dropdown) => {
        dropdown.classList.remove("active");
      });
    }
  });

  // Limpar elementos indesejados (mantido do código original)
  function limparElementos() {
    document
      .querySelectorAll(".apoiadores-right, .menu-mobile, .menu-lateral")
      .forEach((el) => {
        Array.from(el.childNodes).forEach((node) => {
          if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
            node.remove();
          }
        });
      });

    document.querySelectorAll("[data-content]").forEach((el) => {
      el.removeAttribute("data-content");
    });
  }

  limparElementos();

  const observer = new MutationObserver(limparElementos);
  observer.observe(document.body, {
    childList: true,
    subtree: true,
    characterData: true,
  });
});
