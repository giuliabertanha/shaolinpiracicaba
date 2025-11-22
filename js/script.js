//Dropdown
document.addEventListener('DOMContentLoaded', function () {
    const menusDropdown = document.querySelectorAll('.dropdown');

    menusDropdown.forEach(menu => {
        const botao = menu.querySelector('.dropdown-bs-toggle');
        const itensMenu = menu.querySelectorAll('.dropdown-item');
        const campoOculto = menu.previousElementSibling;

        itensMenu.forEach(item => {
            item.addEventListener('click', function (event) {
                event.preventDefault();

                const textoSelecionado = this.textContent;
                botao.textContent = textoSelecionado;

                if (campoOculto && campoOculto.type === 'hidden') {
                    campoOculto.value = textoSelecionado;
                }
            });
        });
    });

    //VALIDAÇÕES DE FORMULÁRIO
    const formulario = document.querySelector('form');
    if (!formulario) return; // Se não houver formulário na página, não faz nada

    const botaoEnviar = document.getElementById('submit-button');
    const camposParaValidar = [
        { id: 'nome', validacao: 'text' },
        { id: 'usuario', validacao: 'alphanumeric' },
        { id: 'senha', validacao: 'length' },
        { id: 'telefone', validacao: 'phone' },
        { id: 'email', validacao: 'length' }
    ];

    const validarFormulario = () => {
        let formularioEValido = true;
        camposParaValidar.forEach(item => {
            const campo = document.getElementById(item.id);
            if (campo && campo.dataset.error === 'true') {
                formularioEValido = false;
            }
        });
        botaoEnviar.disabled = !formularioEValido;
    };

    camposParaValidar.forEach(item => {
        const campo = document.getElementById(item.id);
        const divErro = document.getElementById(`${item.id}-error`);

        if (campo && divErro) {
            campo.addEventListener('input', (e) => {
                let valor = e.target.value;
                const comprimentoMaximo = parseInt(campo.getAttribute('maxlength'), 10);
                let mensagemErro = '';

                // Aplica validações específicas
                if (item.validacao === 'text') {
                    valor = valor.replace(/[^a-zA-Z\sÀ-ú]/g, '');
                } else if (item.validacao === 'alphanumeric') {
                    valor = valor.replace(/[^a-zA-Z0-9]/g, '');
                } else if (item.validacao === 'phone') {
                    valor = valor.replace(/\D/g, '').substring(0, 11);
                    let valorFormatado = '';
                    if (valor.length > 0) valorFormatado = '(' + valor.substring(0, 2);
                    if (valor.length > 2) valorFormatado += ') ' + valor.substring(2, 7);
                    if (valor.length > 7) valorFormatado += '-' + valor.substring(7, 11);
                    valor = valorFormatado;
                }

                e.target.value = valor;

                // Verifica o comprimento
                if (comprimentoMaximo && valor.length > comprimentoMaximo) {
                    mensagemErro = `O campo não pode exceder ${comprimentoMaximo} caracteres.`;
                    campo.dataset.error = 'true';
                } else {
                    campo.dataset.error = 'false';
                }

                divErro.textContent = mensagemErro;
                validarFormulario();
            });
        }
    });

    // Validação inicial para o caso de a página ser recarregada com dados inválidos
    if (botaoEnviar) {
        validarFormulario();
    }
});


    