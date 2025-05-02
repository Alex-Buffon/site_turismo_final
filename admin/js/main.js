$(document).ready(function () {
  // Toggle submenu
  $(".has-submenu > a").click(function (e) {
    e.preventDefault();
    $(this).siblings(".submenu").slideToggle();
  });
});

// Funções da Galeria
function editarImagem(id) {
  fetch(`processar_galeria.php?acao=buscar&id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.sucesso) {
        // Preenche todos os campos incluindo a URL
        document.getElementById("edit_id").value = data.imagem.id;
        document.getElementById("edit_titulo").value = data.imagem.titulo;
        document.getElementById("edit_descricao").value =
          data.imagem.descricao || "";
        document.getElementById("edit_url").value = data.imagem.url || ""; // Garante que a URL seja preenchida

        if (document.getElementById("imagem_atual")) {
          document.getElementById("imagem_atual").src =
            "../uploads/galerias/" + data.imagem.imagem;
        }

        document.getElementById("editarModal").style.display = "block";
      } else {
        alert("Erro ao carregar imagem");
      }
    })
    .catch((error) => {
      console.error("Erro:", error);
      alert("Erro ao carregar imagem");
    });
}

// Form editar
document.addEventListener("DOMContentLoaded", function () {
  const formEdit = document.querySelector("#editarModal form");
  if (formEdit) {
    formEdit.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.append("acao", "editar");

      // Remove notificações anteriores
      document.querySelectorAll(".notification").forEach((n) => n.remove());

      fetch("processar_galeria.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.sucesso) {
            fecharModal(); // Fecha o modal primeiro
            mostrarNotificacao("Imagem atualizada com sucesso!", "success");
            setTimeout(() => {
              window.location.reload();
            }, 2000);
          } else {
            mostrarNotificacao(
              data.mensagem || "Erro ao atualizar imagem",
              "error"
            );
          }
        })
        .catch((error) => {
          console.error("Erro:", error);
          mostrarNotificacao("Erro ao atualizar imagem", "error");
        });
    });
  }
});

// Função para mostrar notificações (versão atualizada)
function mostrarNotificacao(mensagem, tipo) {
  // Remove todas as notificações existentes
  const notificacoesAnteriores = document.querySelectorAll(".notification");
  notificacoesAnteriores.forEach((n) => n.remove());

  const div = document.createElement("div");
  div.className = `alert alert-${tipo} notification`;
  div.style.position = "fixed";
  div.style.top = "20px";
  div.style.right = "20px";
  div.style.zIndex = "9999";
  div.style.padding = "15px 20px";
  div.style.borderRadius = "4px";
  div.style.backgroundColor = tipo === "success" ? "#4CAF50" : "#f44336";
  div.style.color = "white";
  div.textContent = mensagem;

  document.body.appendChild(div);

  // Remove a notificação após 2 segundos
  setTimeout(() => {
    if (div && div.parentElement) {
      div.remove();
    }
  }, 2000);
}

function excluirImagem(id) {
  if (confirm("Deseja realmente excluir esta imagem?")) {
    fetch(`processar_galeria.php?acao=excluir&id=${id}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Erro na requisição");
        }
        return response.json();
      })
      .then((data) => {
        if (data && data.sucesso) {
          alert("Imagem excluída com sucesso!");
          window.location.reload();
        } else {
          throw new Error(data?.mensagem || "Erro ao excluir imagem");
        }
      })
      .catch((error) => {
        console.error("Erro:", error);
        alert("Erro ao excluir imagem");
      });
  }
}

// Validação do formulário de eventos
function validarFormularioEvento(form) {
  const dataInicio = new Date(form.querySelector('[name="data_inicio"]').value);
  const dataFim = new Date(form.querySelector('[name="data_fim"]').value);

  if (dataFim < dataInicio) {
    mostrarNotificacao(
      "A data de término não pode ser anterior à data de início",
      "error"
    );
    return false;
  }

  const imagem = form.querySelector('[name="imagem"]').files[0];
  if (imagem && imagem.size > 2 * 1024 * 1024) {
    mostrarNotificacao("A imagem deve ter no máximo 2MB", "error");
    return false;
  }

  return true;
}

// Função para mostrar notificações
function mostrarNotificacao(mensagem, tipo) {
  const notificacoesAnteriores = document.querySelectorAll(".notification");
  notificacoesAnteriores.forEach((n) => n.remove());

  const div = document.createElement("div");
  div.className = `alert alert-${tipo} notification`;
  div.style.position = "fixed";
  div.style.top = "20px";
  div.style.right = "20px";
  div.style.zIndex = "9999";
  div.textContent = mensagem;

  document.body.appendChild(div);

  setTimeout(() => {
    if (div && div.parentElement) {
      div.remove();
    }
  }, 3000);
}

// Inicialização dos eventos
document.addEventListener("DOMContentLoaded", function () {
  // Abrir modal
  document.querySelectorAll('[data-toggle="modal"]').forEach((button) => {
    button.addEventListener("click", () => {
      const modalId = button.getAttribute("data-target");
      document.querySelector(modalId).style.display = "block";
    });
  });

  // Form adicionar
  const formAdd = document.getElementById("formAddImagem");
  if (formAdd) {
    formAdd.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!validarFormularioEvento(this)) return;

      const formData = new FormData(this);
      formData.append("acao", "adicionar");

      fetch("processar_galeria.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.sucesso) {
            location.reload();
          } else {
            mostrarNotificacao(
              data.mensagem || "Erro ao salvar imagem",
              "error"
            );
          }
        });
    });
  }

  // Form editar
  const formEdit = document.getElementById("formEditarImagem");
  if (formEdit) {
    formEdit.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!validarFormularioEvento(this)) return;

      const formData = new FormData(this);
      formData.append("acao", "editar");

      fetch("processar_galeria.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.sucesso) {
            location.reload();
          } else {
            mostrarNotificacao(
              data.mensagem || "Erro ao atualizar imagem",
              "error"
            );
          }
        });
    });
  }

  // Validação para formulários de eventos
  const formEvento = document.querySelector('form[name="formEvento"]');
  if (formEvento) {
    formEvento.addEventListener("submit", function (e) {
      if (!validarFormularioEvento(this)) {
        e.preventDefault();
      }
    });
  }

  // Funções para Galeria de Serviços
  function abrirModal(id = null) {
    document.getElementById("formServico").reset();
    document.getElementById("id").value = "";
    document.getElementById("preview-container").style.display = "none";

    if (id) {
      // Modo edição
      fetch(`processar_galeria.php?acao=buscar&id=${id}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.sucesso) {
            document.getElementById("id").value = data.imagem.id;
            document.getElementById("titulo").value = data.imagem.titulo;
            document.getElementById("descricao").value = data.imagem.descricao;
            if (data.imagem.imagem) {
              document.getElementById("preview-container").style.display =
                "block";
              document.getElementById("imagem_preview").src =
                "../uploads/galerias/" + data.imagem.imagem;
            }
          }
        });
    }

    $("#servicoModal").modal("show");
  }

  function fecharModal() {
    $("#servicoModal").modal("hide");
  }

  function salvarServico() {
    const form = document.getElementById("formServico");
    const formData = new FormData(form);

    fetch("processar_galeria.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.sucesso) {
          fecharModal();
          location.reload();
        } else {
          alert(data.mensagem);
        }
      })
      .catch((error) => console.error("Erro:", error));
  }

  function excluirServico(id) {
    if (confirm("Tem certeza que deseja excluir este serviço?")) {
      fetch(`processar_galeria.php?acao=excluir&id=${id}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.sucesso) {
            location.reload();
          } else {
            alert(data.mensagem);
          }
        })
        .catch((error) => console.error("Erro:", error));
    }
  }
});
