// Configurações globais
const API_CONFIG = {
    REST_COUNTRIES: 'https://restcountries.com/v3.1/name/',
    OPENWEATHER: 'https://api.openweathermap.org/data/2.5/weather',
    OPENWEATHER_KEY: '' // Deixe vazio ou use chave gratuita
};

// Utilidades
class Utils {
    static formatarNumero(numero) {
        return new Intl.NumberFormat('pt-BR').format(numero);
    }

    static mostrarLoading(elemento) {
        elemento.innerHTML = '<div class="loading"></div>';
    }

    static mostrarErro(mensagem) {
        alert(`Erro: ${mensagem}`);
    }

    static confirmarAcao(mensagem) {
        return confirm(mensagem);
    }
}

// Carregar estatísticas na página inicial
class Estatisticas {
    static async carregar() {
        try {
            const response = await fetch('backend/paises/listar.php');
            const paises = await response.json();
            
            const responseCidades = await fetch('backend/cidades/listar.php');
            const cidades = await responseCidades.json();

            this.atualizarUI(paises, cidades);
            this.carregarEstatisticasContinentais(paises, cidades);
        } catch (error) {
            console.error('Erro ao carregar estatísticas:', error);
        }
    }

    static atualizarUI(paises, cidades) {
        document.getElementById('total-paises').textContent = paises.length;
        document.getElementById('total-cidades').textContent = cidades.length;
        
        const populacaoTotal = paises.reduce((total, pais) => total + parseInt(pais.populacao), 0);
        document.getElementById('populacao-mundial').textContent = Utils.formatarNumero(populacaoTotal);
    }

    static carregarEstatisticasContinentais(paises, cidades) {
        const statsContinentais = {};
        
        paises.forEach(pais => {
            const continente = pais.continente;
            if (!statsContinentais[continente]) {
                statsContinentais[continente] = {
                    paises: 0,
                    cidades: 0,
                    populacao: 0
                };
            }
            statsContinentais[continente].paises++;
            statsContinentais[continente].populacao += parseInt(pais.populacao);
        });

        cidades.forEach(cidade => {
            const pais = paises.find(p => p.id_pais == cidade.id_pais);
            if (pais && statsContinentais[pais.continente]) {
                statsContinentais[pais.continente].cidades++;
            }
        });

        this.exibirEstatisticasContinentais(statsContinentais);
    }

    static exibirEstatisticasContinentais(stats) {
        const container = document.getElementById('stats-continente');
        if (!container) return;

        container.innerHTML = Object.entries(stats).map(([continente, dados]) => `
            <div class="stat-card">
                <h3>${continente}</h3>
                <p>Países: ${dados.paises}</p>
                <p>Cidades: ${dados.cidades}</p>
                <p>População: ${Utils.formatarNumero(dados.populacao)}</p>
            </div>
        `).join('');
    }
}

// Gerenciamento de Modais
class ModalManager {
    static abrirModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    static fecharModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    static fecharModalAoClicarFora(event, modalId) {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            this.fecharModal(modalId);
        }
    }
}

// Inicialização da aplicação
document.addEventListener('DOMContentLoaded', function() {
    // Carregar estatísticas se estiver na página inicial
    if (document.getElementById('total-paises')) {
        Estatisticas.carregar();
    }

    // Configurar event listeners para modais
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(event) {
            ModalManager.fecharModalAoClicarFora(event, modal.id);
        });
    });

    // Adicionar máscaras para números
    const inputsNumero = document.querySelectorAll('input[type="number"]');
    inputsNumero.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = Utils.formatarNumero(parseInt(this.value.replace(/\D/g, '')));
            }
        });
        
        input.addEventListener('focus', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    });
});

// Funções globais para uso em outras páginas
function fecharModal() {
    ModalManager.fecharModal('modal-pais');
}

function fecharModalCidade() {
    ModalManager.fecharModal('modal-cidade');
}

function fecharModalApi() {
    ModalManager.fecharModal('modal-api-info');
}

function fecharModalClima() {
    ModalManager.fecharModal('modal-clima');
}