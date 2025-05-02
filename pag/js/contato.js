document.addEventListener("DOMContentLoaded", function () {
  const formContato = document.getElementById("formContato");

  if (formContato) {
    // Envio do formulário
    formContato.addEventListener("submit", function (e) {
      e.preventDefault();
      const btnSubmit = this.querySelector('button[type="submit"]');
      btnSubmit.disabled = true;
      btnSubmit.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Enviando...';

      // Caminho correto para o processamento
      fetch("../admin/contatos/processar_contato.php", {
        method: "POST",
        body: new FormData(this),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            mostrarMensagem(data.message, "success");
            formContato.reset();
          } else {
            throw new Error(data.message);
          }
        })
        .catch((error) => {
          mostrarMensagem("Erro ao enviar mensagem: " + error.message, "error");
        })
        .finally(() => {
          btnSubmit.disabled = false;
          btnSubmit.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar';
        });
    });

    function mostrarMensagem(texto, tipo) {
      const mensagem = document.createElement("div");
      mensagem.className = `alert alert-${tipo}`;
      mensagem.innerHTML = texto;

      formContato.insertAdjacentElement("beforebegin", mensagem);

      setTimeout(() => mensagem.remove(), 5000);
    }
  }
});
