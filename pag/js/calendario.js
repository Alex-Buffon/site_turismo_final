document.addEventListener("DOMContentLoaded", function () {
  // Elementos DOM
  const mesesTabs = document.querySelectorAll(".mes-tab");
  const eventosGrids = document.querySelectorAll(".eventos-grid");
  const modal = document.getElementById("modal-evento");
  const btnFechar = modal.querySelector(".fechar");

  // Estado global
  let eventosDoMesAtual = [];
  let eventoAtualIndex = 0;

  // Array global de meses
  const meses = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro",
  ];

  // Função auxiliar para obter nome do mês
  function obterNomeMes(mesIndex) {
    return meses[mesIndex];
  }

  // Função para gerar sequência de meses
  function atualizarSequenciaMeses() {
    const hoje = new Date();
    const dataBase = new Date(hoje.getFullYear(), hoje.getMonth());
    const container = document.querySelector(".meses-tabs");

    if (!container) return;
    container.innerHTML = "";

    // Gera sequência de 12 meses a partir do atual
    for (let i = 0; i < 12; i++) {
      const data = new Date(dataBase);
      data.setMonth(dataBase.getMonth() + i);

      const tab = document.createElement("button");
      tab.className = "mes-tab";
      tab.setAttribute("data-mes", data.getMonth() + 1);
      tab.setAttribute("data-ano", data.getFullYear());
      tab.textContent = `${meses[data.getMonth()]} / ${data.getFullYear()}`;

      if (i === 0) tab.classList.add("active");
      container.appendChild(tab);
    }

    // Atualiza eventos para o mês atual
    const mesAtual = hoje.getMonth() + 1;
    trocarMes(mesAtual);
  }

  // Funções principais
  function inicializar() {
    // Gera sequência inicial de meses
    atualizarSequenciaMeses();

    // Event Listeners principais
    document.querySelectorAll(".mes-tab").forEach((tab) => {
      tab.addEventListener("click", () => trocarMes(tab.dataset.mes));
    });

    btnFechar.addEventListener("click", fecharModal);

    modal.addEventListener("click", (e) => {
      if (e.target === modal) fecharModal();
    });

    // Controles de teclado
    document.addEventListener("keydown", (e) => {
      if (modal.style.display === "flex") {
        if (e.key === "ArrowLeft") navegarEvento("prev");
        if (e.key === "ArrowRight") navegarEvento("next");
        if (e.key === "Escape") fecharModal();
      }
    });

    // Remove eventos passados e inicia com mês atual
    removerEventosPassados();
  }

  function removerEventosPassados() {
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    document.querySelectorAll(".eventos-grid").forEach((grid) => {
      const eventos = Array.from(grid.querySelectorAll(".evento-card"));
      let temEventosAtivos = false;

      eventos.forEach((card) => {
        const info = JSON.parse(card.dataset.info);
        const [ano, mes, dia] = info.data_completa.split("-").map(Number);
        const dataEvento = new Date(ano, mes - 1, dia);
        dataEvento.setHours(0, 0, 0, 0);

        if (dataEvento.getTime() >= hoje.getTime()) {
          temEventosAtivos = true;
          card.style.display = "block";
        } else {
          card.remove();
        }
      });

      if (!temEventosAtivos) {
        const mesNumero = grid.dataset.mes;
        const mesTab = document.querySelector(`[data-mes="${mesNumero}"]`);
        const anoGrid = mesTab
          ? mesTab.getAttribute("data-ano")
          : hoje.getFullYear();
        const nomeMes = obterNomeMes(parseInt(mesNumero) - 1);

        grid.innerHTML = `
                    <div class="sem-eventos">
                        <p>Nenhum evento programado para ${nomeMes} de ${anoGrid}</p>
                    </div>
                `;
      }
    });

    const mesAtual = hoje.getMonth() + 1;
    trocarMes(mesAtual);
  }

  function trocarMes(mesNumero) {
    // Remove apenas a classe active, sem mexer em estilos inline
    mesesTabs.forEach((tab) => {
      tab.classList.remove("active");
    });

    // Adiciona classe active na nova tab
    const tabAtiva = document.querySelector(`[data-mes="${mesNumero}"]`);
    if (tabAtiva) {
      tabAtiva.classList.add("active");
    }

    // Atualiza grids
    eventosGrids.forEach((grid) => (grid.style.display = "none"));
    const gridAtivo = document.getElementById(`mes-${mesNumero}`);
    if (gridAtivo) {
      gridAtivo.style.display = "grid";
      carregarEventosDoMes(mesNumero);
    }
  }
  function carregarEventosDoMes(mesNumero) {
    const gridAtivo = document.getElementById(`mes-${mesNumero}`);
    if (!gridAtivo) return;

    eventosDoMesAtual = Array.from(gridAtivo.querySelectorAll(".evento-card"));

    eventosDoMesAtual.forEach((card) => {
      const novoCard = card.cloneNode(true);
      card.parentNode.replaceChild(novoCard, card);
    });

    eventosDoMesAtual = Array.from(gridAtivo.querySelectorAll(".evento-card"));
    eventosDoMesAtual.forEach((card, index) => {
      card.addEventListener("click", () => abrirEvento(card, index));
    });
  }

  function abrirEvento(elemento, index) {
    eventoAtualIndex = index;
    const info = JSON.parse(elemento.dataset.info);
    mostrarEvento(info);
  }

  function mostrarEvento(info) {
    const detalhes = document.getElementById("evento-detalhes");
    const totalEventos = eventosDoMesAtual.length;

    const temEventoAnterior = eventoAtualIndex > 0;
    const temProximoEvento = eventoAtualIndex < totalEventos - 1;

    detalhes.innerHTML = `
            ${
              temEventoAnterior
                ? `
                <button class="nav-btn prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `
                : ""
            }
            ${
              temProximoEvento
                ? `
                <button class="nav-btn next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `
                : ""
            }
            <div class="modal-header">
                <img src="../admin/uploads/eventos/${info.imagem}" alt="${
      info.titulo
    }">
            </div>
            <div class="modal-body">
                <h2>${info.titulo}</h2>
                <p class="evento-data-modal">
                    <i class="fas fa-calendar"></i> ${info.dia} de ${info.mes}
                </p>
                <p class="evento-local-modal">
                    <i class="fas fa-map-marker-alt"></i> ${info.local}
                </p>
            </div>
        `;

    const prevBtn = detalhes.querySelector(".nav-btn.prev");
    const nextBtn = detalhes.querySelector(".nav-btn.next");

    if (prevBtn) prevBtn.addEventListener("click", () => navegarEvento("prev"));
    if (nextBtn) nextBtn.addEventListener("click", () => navegarEvento("next"));

    modal.style.display = "flex";
  }

  function navegarEvento(direcao) {
    const novoIndex =
      direcao === "prev" ? eventoAtualIndex - 1 : eventoAtualIndex + 1;

    if (novoIndex >= 0 && novoIndex < eventosDoMesAtual.length) {
      eventoAtualIndex = novoIndex;
      const eventoElement = eventosDoMesAtual[eventoAtualIndex];
      const info = JSON.parse(eventoElement.dataset.info);
      mostrarEvento(info);
    }
  }

  function fecharModal() {
    modal.style.display = "none";
  }

  // Inicia o calendário
  inicializar();
});
