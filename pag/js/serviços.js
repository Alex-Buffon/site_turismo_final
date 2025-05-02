document.addEventListener("DOMContentLoaded", function () {
  const botoes = document.querySelectorAll(".categoria-btn");
  const grids = document.querySelectorAll(".servicos-grid");
  const servicosLinks = document.querySelectorAll(
    ".submenu a, .submenu-mobile a"
  );
  const menuLateral = document.querySelector(".menu-lateral");
  const menuToggle = document.querySelector(".menu-toggle");

  // Função para fechar menu mobile
  function fecharMenuMobile() {
    menuLateral?.classList.remove("active");
    menuToggle?.classList.remove("active");
    document.body.style.overflow = "";
  }

  // Adiciona listeners para cards de serviços
  function adicionarLinksServicos() {
    const servicosCards = document.querySelectorAll(".servico-card");

    servicosCards.forEach((card) => {
      if (card.dataset.url) {
        card.style.cursor = "pointer";
        card.addEventListener("click", function () {
          window.open(this.dataset.url, "_blank");
        });
      }
    });
  }

  // Função para ativar categoria
  function ativarCategoria(categoria) {
    // Remove classe ativa de todos os botões
    botoes.forEach((b) => b.classList.remove("active"));

    // Ativa o botão correspondente
    const botaoAtivo = document.querySelector(
      `[data-categoria="${categoria}"]`
    );
    if (botaoAtivo) {
      botaoAtivo.classList.add("active");
    }

    // Esconde todos os grids
    grids.forEach((grid) => {
      grid.style.display = "none";
      grid.classList.remove("active");
    });

    // Mostra o grid da categoria selecionada
    const gridAtivo = document.getElementById(`${categoria}-grid`);
    if (gridAtivo) {
      gridAtivo.style.display = "grid";
      gridAtivo.classList.add("active");

      // Adiciona os links após mostrar o grid
      adicionarLinksServicos();
    }
  }

  // Event listeners para links do menu (desktop e mobile)
  servicosLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      const categoriaId = this.getAttribute("href")
        .substring(1)
        .replace("-grid", "");

      // Fecha o menu mobile primeiro
      fecharMenuMobile();

      // Pequeno delay para garantir transição suave
      setTimeout(() => {
        ativarCategoria(categoriaId);

        // Scroll suave até a seção ajustando a altura
        const servicosSection = document.querySelector("#servicos");
        if (servicosSection) {
          const headerHeight =
            document.querySelector(".header-principal").offsetHeight;
          const targetPosition =
            servicosSection.getBoundingClientRect().top +
            window.pageYOffset -
            headerHeight;

          window.scrollTo({
            top: targetPosition,
            behavior: "smooth",
          });
        }
      }, 300);
    });
  });

  // Ativar primeira categoria por padrão
  if (botoes.length > 0) {
    const primeiraCategoria = botoes[0].dataset.categoria;
    ativarCategoria(primeiraCategoria);
  }

  // Event listeners para botões de categoria
  botoes.forEach((botao) => {
    botao.addEventListener("click", function () {
      ativarCategoria(this.dataset.categoria);
    });
  });

  // Inicializa os links na carga inicial
  adicionarLinksServicos();
});
