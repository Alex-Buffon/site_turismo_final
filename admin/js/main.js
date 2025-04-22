$(document).ready(function() {
    // Toggle submenu
    $('.has-submenu > a').click(function(e) {
        e.preventDefault();
        $(this).siblings('.submenu').slideToggle();
    });
});

// Funções da Galeria
function editarImagem(id) {
    fetch(`processar_galeria.php?acao=buscar&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.sucesso) {
                document.getElementById('edit_id').value = data.imagem.id;
                document.getElementById('edit_titulo').value = data.imagem.titulo;
                document.getElementById('edit_descricao').value = data.imagem.descricao;
                if(document.getElementById('imagem_atual')) {
                    document.getElementById('imagem_atual').src = `../uploads/galeria/${data.imagem.imagem}`;
                }
                document.getElementById('editarModal').style.display = 'block';
            } else {
                mostrarNotificacao('Erro ao carregar imagem', 'error');
            }
        });
}

function excluirImagem(id) {
    if(confirm('Deseja realmente excluir esta imagem?')) {
        fetch(`processar_galeria.php?acao=excluir&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    location.reload();
                } else {
                    mostrarNotificacao('Erro ao excluir imagem', 'error');
                }
            });
    }
}

function fecharModal() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
}

// Validação do formulário de eventos
function validarFormularioEvento(form) {
    const dataInicio = new Date(form.querySelector('[name="data_inicio"]').value);
    const dataFim = new Date(form.querySelector('[name="data_fim"]').value);

    if (dataFim < dataInicio) {
        mostrarNotificacao('A data de término não pode ser anterior à data de início', 'error');
        return false;
    }

    const imagem = form.querySelector('[name="imagem"]').files[0];
    if (imagem && imagem.size > 2 * 1024 * 1024) {
        mostrarNotificacao('A imagem deve ter no máximo 2MB', 'error');
        return false;
    }

    return true;
}

// Função para mostrar notificações
function mostrarNotificacao(mensagem, tipo) {
    const notificacoesAnteriores = document.querySelectorAll('.notification');
    notificacoesAnteriores.forEach(n => n.remove());

    const div = document.createElement('div');
    div.className = `alert alert-${tipo} notification`;
    div.style.position = 'fixed';
    div.style.top = '20px';
    div.style.right = '20px';
    div.style.zIndex = '9999';
    div.textContent = mensagem;

    document.body.appendChild(div);

    setTimeout(() => {
        if(div && div.parentElement) {
            div.remove();
        }
    }, 3000);
}

// Inicialização dos eventos
document.addEventListener('DOMContentLoaded', function() {
    // Abrir modal
    document.querySelectorAll('[data-toggle="modal"]').forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-target');
            document.querySelector(modalId).style.display = 'block';
        });
    });

    // Form adicionar
    const formAdd = document.getElementById('formAddImagem');
    if(formAdd) {
        formAdd.addEventListener('submit', function(e) {
            e.preventDefault();
            if(!validarFormularioEvento(this)) return;

            const formData = new FormData(this);
            formData.append('acao', 'adicionar');

            fetch('processar_galeria.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    location.reload();
                } else {
                    mostrarNotificacao(data.mensagem || 'Erro ao salvar imagem', 'error');
                }
            });
        });
    }

    // Form editar
    const formEdit = document.getElementById('formEditarImagem');
    if(formEdit) {
        formEdit.addEventListener('submit', function(e) {
            e.preventDefault();
            if(!validarFormularioEvento(this)) return;

            const formData = new FormData(this);
            formData.append('acao', 'editar');

            fetch('processar_galeria.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    location.reload();
                } else {
                    mostrarNotificacao(data.mensagem || 'Erro ao atualizar imagem', 'error');
                }
            });
        });
    }

    // Validação para formulários de eventos
    const formEvento = document.querySelector('form[name="formEvento"]');
    if(formEvento) {
        formEvento.addEventListener('submit', function(e) {
            if(!validarFormularioEvento(this)) {
                e.preventDefault();
            }
        });
    }
});
