class GerenciadorCidades {
    constructor() {
        this.cidades = [];
        this.paises = [];
        this.init();
    }

    async init() {
        await this.carregarPaises();
        await this.carregarCidades();
        this.configurarEventListeners();
    }

    async carregarPaises() {
        try {
            const response = await fetch('backend/paises/listar.php');
            this.paises = await response.json();
            this.preencherSelectPaises();
        } catch (error) {
            console.error('Erro ao carregar países:', error);
        }
    }

    async carregarCidades() {
        try {
            Utils.mostrarLoading(document.getElementById('lista-cidades'));
            
            const response = await fetch('backend/cidades/listar.php');
            const dados = await response.json();
            this.cidades = Array.isArray(dados) ? dados : [];
            
            this.exibirCidades(this.cidades);
        } catch (error) {
            console.error('Erro ao carregar cidades:', error);
            Utils.mostrarErro('Erro ao carregar cidades');
        }
    }

    preencherSelectPaises() {
        const selectPaises = document.getElementById('cidade-pais');
        const selectFiltro = document.getElementById('filtro-pais');
        
        if (selectPaises) {
            selectPaises.innerHTML = '<option value="">Selecione um país...</option>' +
                this.paises.map(pais => 
                    `<option value="${pais.id_pais}">${pais.nome}</option>`
                ).join('');
        }
        
        if (selectFiltro) {
            selectFiltro.innerHTML = '<option value="">Todos os países</option>' +
                this.paises.map(pais => 
                    `<option value="${pais.id_pais}">${pais.nome}</option>`
                ).join('');
        }
    }

    exibirCidades(cidades) {
        const tbody = document.getElementById('lista-cidades');
        
        if (cidades.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Nenhuma cidade cadastrada</td></tr>';
            return;
        }

        tbody.innerHTML = cidades.map(cidade => {
            const pais = this.paises.find(p => p.id_pais == cidade.id_pais);
            const coordenadas = cidade.latitude && cidade.longitude ? 
                `${parseFloat(cidade.latitude).toFixed(4)}, ${parseFloat(cidade.longitude).toFixed(4)}` : 
                'N/A';
            
            return `
                <tr>
                    <td>${cidade.nome}</td>
                    <td>${Utils.formatarNumero(cidade.populacao)}</td>
                    <td>${pais ? pais.nome : 'N/A'}</td>
                    <td>${coordenadas}</td>
                    <td class="actions-cell">
                        <button class="btn btn-success" onclick="gerenciadorCidades.editarCidade(${cidade.id_cidade})">✏️ Editar</button>
                        <button class="btn btn-info" onclick="gerenciadorCidades.buscarClima('${cidade.nome}', ${cidade.latitude}, ${cidade.longitude})">🌤️ Clima</button>
                        <button class="btn btn-danger" onclick="gerenciadorCidades.excluirCidade(${cidade.id_cidade})">🗑️ Excluir</button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    abrirModalCriarCidade() {
        document.getElementById('modal-titulo-cidade').textContent = 'Nova Cidade';
        document.getElementById('form-cidade').reset();
        document.getElementById('cidade-id').value = '';
        ModalManager.abrirModal('modal-cidade');
    }

    async salvarCidade(event) {
        event.preventDefault();
        
        const formData = new FormData();
        formData.append('id_cidade', document.getElementById('cidade-id').value);
        formData.append('nome', document.getElementById('cidade-nome').value);
        formData.append('populacao', document.getElementById('cidade-populacao').value.replace(/\D/g, ''));
        formData.append('id_pais', document.getElementById('cidade-pais').value);
        formData.append('latitude', document.getElementById('cidade-latitude').value);
        formData.append('longitude', document.getElementById('cidade-longitude').value);

        try {
            const url = formData.get('id_cidade') ? 'backend/cidades/atualizar.php' : 'backend/cidades/criar.php';
            
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (resultado.success) {
                fecharModalCidade();
                await this.carregarCidades();
                
                // Atualizar estatísticas na página inicial
                if (typeof Estatisticas !== 'undefined') {
                    Estatisticas.carregar();
                }
            } else {
                Utils.mostrarErro(resultado.message || 'Erro ao salvar cidade');
            }
        } catch (error) {
            console.error('Erro ao salvar cidade:', error);
            Utils.mostrarErro('Erro ao salvar cidade');
        }
    }

    async editarCidade(idCidade) {
        try {
            const response = await fetch(`backend/cidades/editar.php?id=${idCidade}`);
            const cidade = await response.json();

            if (cidade) {
                document.getElementById('modal-titulo-cidade').textContent = 'Editar Cidade';
                document.getElementById('cidade-id').value = cidade.id_cidade;
                document.getElementById('cidade-nome').value = cidade.nome;
                document.getElementById('cidade-populacao').value = Utils.formatarNumero(cidade.populacao);
                document.getElementById('cidade-pais').value = cidade.id_pais;
                document.getElementById('cidade-latitude').value = cidade.latitude || '';
                document.getElementById('cidade-longitude').value = cidade.longitude || '';

                ModalManager.abrirModal('modal-cidade');
            }
        } catch (error) {
            console.error('Erro ao carregar cidade para edição:', error);
            Utils.mostrarErro('Erro ao carregar cidade');
        }
    }

    async excluirCidade(idCidade) {
        const cidade = this.cidades.find(c => c.id_cidade == idCidade);
        
        if (!Utils.confirmarAcao(`Tem certeza que deseja excluir a cidade "${cidade.nome}"?`)) {
            return;
        }

        try {
            const response = await fetch('backend/cidades/excluir.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${idCidade}`
            });

            const resultado = await response.json();

            if (resultado.success) {
                await this.carregarCidades();
                
                // Atualizar estatísticas na página inicial
                if (typeof Estatisticas !== 'undefined') {
                    Estatisticas.carregar();
                }
            } else {
                Utils.mostrarErro(resultado.message || 'Erro ao excluir cidade');
            }
        } catch (error) {
            console.error('Erro ao excluir cidade:', error);
            Utils.mostrarErro('Erro ao excluir cidade');
        }
    }

    async buscarClima(nomeCidade, latitude, longitude) {
        try {
            Utils.mostrarLoading(document.getElementById('clima-content'));
            ModalManager.abrirModal('modal-clima');

            let url;
            if (latitude && longitude) {
                url = `${API_CONFIG.OPENWEATHER}?lat=${latitude}&lon=${longitude}&appid=${API_CONFIG.OPENWEATHER_KEY}&units=metric&lang=pt_br`;
            } else {
                url = `${API_CONFIG.OPENWEATHER}?q=${encodeURIComponent(nomeCidade)}&appid=${API_CONFIG.OPENWEATHER_KEY}&units=metric&lang=pt_br`;
            }

            const response = await fetch(url);
            const dados = await response.json();

            if (dados.cod === 200) {
                this.exibirClima(dados);
            } else {
                document.getElementById('clima-content').innerHTML = '<p>Não foi possível obter informações do clima.</p>';
            }
        } catch (error) {
            console.error('Erro ao buscar informações do clima:', error);
            document.getElementById('clima-content').innerHTML = '<p>Erro ao carregar informações do clima.</p>';
        }
    }

    exibirClima(dados) {
        const content = document.getElementById('clima-content');
        const temperatura = Math.round(dados.main.temp);
        const sensacao = Math.round(dados.main.feels_like);
        const icone = dados.weather[0].icon;
        const descricao = dados.weather[0].description;
        
        content.innerHTML = `
            <div class="weather-card">
                <h4>${dados.name}, ${dados.sys.country}</h4>
                <div class="weather-temp">
                    <img src="https://openweathermap.org/img/wn/${icone}@2x.png" alt="${descricao}">
                    ${temperatura}°C
                </div>
                <p style="text-transform: capitalize;">${descricao}</p>
                <div class="weather-details">
                    <div>
                        <strong>Sensação Térmica</strong><br>
                        ${sensacao}°C
                    </div>
                    <div>
                        <strong>Umidade</strong><br>
                        ${dados.main.humidity}%
                    </div>
                    <div>
                        <strong>Pressão</strong><br>
                        ${dados.main.pressure} hPa
                    </div>
                    <div>
                        <strong>Vento</strong><br>
                        ${dados.wind.speed} m/s
                    </div>
                </div>
            </div>
        `;
    }

    filtrarCidades() {
        const termo = document.getElementById('search-cidades').value.toLowerCase();
        const paisFiltro = document.getElementById('filtro-pais').value;
        
        const cidadesFiltradas = this.cidades.filter(cidade => {
            const correspondeTermo = cidade.nome.toLowerCase().includes(termo);
            const correspondePais = !paisFiltro || cidade.id_pais == paisFiltro;
            return correspondeTermo && correspondePais;
        });
        
        this.exibirCidades(cidadesFiltradas);
    }

    configurarEventListeners() {
        // Fechar modal com ESC
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                fecharModalCidade();
                fecharModalClima();
            }
        });
    }
}

// Inicializar gerenciador de cidades
const gerenciadorCidades = new GerenciadorCidades();

// Funções globais para o HTML
function abrirModalCriarCidade() {
    gerenciadorCidades.abrirModalCriarCidade();
}

function salvarCidade(event) {
    gerenciadorCidades.salvarCidade(event);
}

function filtrarCidades() {
    gerenciadorCidades.filtrarCidades();
}