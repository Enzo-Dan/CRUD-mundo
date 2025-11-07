class GerenciadorPaises {
    constructor() {
        this.paises = [];
        this.init();
    }

    async init() {
        await this.carregarPaises();
        this.configurarEventListeners();
    }

    async carregarPaises() {
        try {
            Utils.mostrarLoading(document.getElementById('lista-paises'));
            
            const response = await fetch('backend/paises/listar.php');
            this.paises = await response.json();
            
            this.exibirPaises(this.paises);
        } catch (error) {
            console.error('Erro ao carregar países:', error);
            Utils.mostrarErro('Erro ao carregar países');
        }
    }

    exibirPaises(paises) {
        const tbody = document.getElementById('lista-paises');
        
        if (paises.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Nenhum país cadastrado</td></tr>';
            return;
        }

        tbody.innerHTML = paises.map(pais => `
            <tr>
                <td>${pais.nome}</td>
                <td>${pais.nome_oficial}</td>
                <td><span class="badge badge-info">${pais.continente}</span></td>
                <td>${Utils.formatarNumero(pais.populacao)}</td>
                <td>${pais.idioma}</td>
                <td class="actions-cell">
                    <button class="btn btn-success" onclick="gerenciadorPaises.editarPais(${pais.id_pais})">✏️ Editar</button>
                    <button class="btn btn-info" onclick="gerenciadorPaises.buscarInfoAPI('${pais.nome}')">🌐 API Info</button>
                    <button class="btn btn-danger" onclick="gerenciadorPaises.excluirPais(${pais.id_pais})">🗑️ Excluir</button>
                </td>
            </tr>
        `).join('');
    }

    abrirModalCriar() {
        document.getElementById('modal-titulo').textContent = 'Novo País';
        document.getElementById('form-pais').reset();
        document.getElementById('pais-id').value = '';
        ModalManager.abrirModal('modal-pais');
    }

    async salvarPais(event) {
        event.preventDefault();
        
        const formData = new FormData();
        formData.append('id_pais', document.getElementById('pais-id').value);
        formData.append('nome', document.getElementById('pais-nome').value);
        formData.append('nome_oficial', document.getElementById('pais-nome-oficial').value);
        formData.append('continente', document.getElementById('pais-continente').value);
        formData.append('populacao', document.getElementById('pais-populacao').value.replace(/\D/g, ''));
        formData.append('idioma', document.getElementById('pais-idioma').value);
        formData.append('moeda', document.getElementById('pais-moeda').value);
        formData.append('capital', document.getElementById('pais-capital').value);

        try {
            const url = formData.get('id_pais') ? 'backend/paises/atualizar.php' : 'backend/paises/criar.php';
            
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (resultado.success) {
                fecharModal();
                await this.carregarPaises();
                
                // Atualizar estatísticas na página inicial
                if (typeof Estatisticas !== 'undefined') {
                    Estatisticas.carregar();
                }
            } else {
                Utils.mostrarErro(resultado.message || 'Erro ao salvar país');
            }
        } catch (error) {
            console.error('Erro ao salvar país:', error);
            Utils.mostrarErro('Erro ao salvar país');
        }
    }

    async editarPais(idPais) {
        try {
            const response = await fetch(`backend/paises/editar.php?id=${idPais}`);
            const pais = await response.json();

            if (pais) {
                document.getElementById('modal-titulo').textContent = 'Editar País';
                document.getElementById('pais-id').value = pais.id_pais;
                document.getElementById('pais-nome').value = pais.nome;
                document.getElementById('pais-nome-oficial').value = pais.nome_oficial;
                document.getElementById('pais-continente').value = pais.continente;
                document.getElementById('pais-populacao').value = Utils.formatarNumero(pais.populacao);
                document.getElementById('pais-idioma').value = pais.idioma;
                document.getElementById('pais-moeda').value = pais.moeda || '';
                document.getElementById('pais-capital').value = pais.capital || '';

                ModalManager.abrirModal('modal-pais');
            }
        } catch (error) {
            console.error('Erro ao carregar país para edição:', error);
            Utils.mostrarErro('Erro ao carregar país');
        }
    }

    async excluirPais(idPais) {
        const pais = this.paises.find(p => p.id_pais == idPais);
        
        if (!Utils.confirmarAcao(`Tem certeza que deseja excluir o país "${pais.nome}"?`)) {
            return;
        }

        try {
            const response = await fetch('backend/paises/excluir.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${idPais}`
            });

            const resultado = await response.json();

            if (resultado.success) {
                await this.carregarPaises();
                
                // Atualizar estatísticas na página inicial
                if (typeof Estatisticas !== 'undefined') {
                    Estatisticas.carregar();
                }
            } else {
                Utils.mostrarErro(resultado.message || 'Erro ao excluir país');
            }
        } catch (error) {
            console.error('Erro ao excluir país:', error);
            Utils.mostrarErro('Erro ao excluir país');
        }
    }

    async buscarInfoAPI(nomePais) {
        try {
            Utils.mostrarLoading(document.getElementById('api-info-content'));
            ModalManager.abrirModal('modal-api-info');

            const response = await fetch(`${API_CONFIG.REST_COUNTRIES}${encodeURIComponent(nomePais)}`);
            const dados = await response.json();

            if (dados && dados.length > 0) {
                const pais = dados[0];
                this.exibirInfoAPI(pais);
            } else {
                document.getElementById('api-info-content').innerHTML = '<p>Nenhuma informação encontrada para este país.</p>';
            }
        } catch (error) {
            console.error('Erro ao buscar informações da API:', error);
            document.getElementById('api-info-content').innerHTML = '<p>Erro ao carregar informações da API.</p>';
        }
    }

    exibirInfoAPI(pais) {
        const content = document.getElementById('api-info-content');
        
        content.innerHTML = `
            <div class="api-info-card">
                <h4>${pais.name?.common || 'N/A'}</h4>
                <p><strong>Nome Oficial:</strong> ${pais.name?.official || 'N/A'}</p>
                <p><strong>Capital:</strong> ${pais.capital?.[0] || 'N/A'}</p>
                <p><strong>Região:</strong> ${pais.region || 'N/A'}</p>
                <p><strong>Sub-região:</strong> ${pais.subregion || 'N/A'}</p>
                <p><strong>População:</strong> ${Utils.formatarNumero(pais.population || 0)}</p>
                <p><strong>Área:</strong> ${Utils.formatarNumero(pais.area || 0)} km²</p>
                <p><strong>Idiomas:</strong> ${pais.languages ? Object.values(pais.languages).join(', ') : 'N/A'}</p>
                <p><strong>Moeda:</strong> ${pais.currencies ? Object.values(pais.currencies).map(c => c.name).join(', ') : 'N/A'}</p>
                <p><strong>Fuso Horário:</strong> ${pais.timezones?.[0] || 'N/A'}</p>
                ${pais.flags?.png ? `<img src="${pais.flags.png}" alt="Bandeira" style="max-width: 100px; margin-top: 10px;">` : ''}
            </div>
        `;
    }

    filtrarPaises() {
        const termo = document.getElementById('search-paises').value.toLowerCase();
        const paisesFiltrados = this.paises.filter(pais => 
            pais.nome.toLowerCase().includes(termo) ||
            pais.nome_oficial.toLowerCase().includes(termo) ||
            pais.continente.toLowerCase().includes(termo) ||
            pais.idioma.toLowerCase().includes(termo)
        );
        this.exibirPaises(paisesFiltrados);
    }

    configurarEventListeners() {
        // Fechar modal com ESC
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                fecharModal();
                fecharModalApi();
            }
        });
    }
}

// Inicializar gerenciador de países
const gerenciadorPaises = new GerenciadorPaises();

// Funções globais para o HTML
function abrirModalCriar() {
    gerenciadorPaises.abrirModalCriar();
}

function salvarPais(event) {
    gerenciadorPaises.salvarPais(event);
}

function filtrarPaises() {
    gerenciadorPaises.filtrarPaises();
}