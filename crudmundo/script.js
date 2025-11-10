// 🎯 FUNÇÕES DE MODAL

function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

// Fechar modal ao clicar fora
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal.id);
    });
});

// ESC para fechar
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.active').forEach(m => {
            closeModal(m.id);
        });
    }
});

// 🗑️ CONFIRMAÇÃO DE EXCLUSÃO

function confirmDelete(type, id, name) {
    if (confirm(`Tem certeza que deseja deletar "${name}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_${type}">
            <input type="hidden" name="id" value="${id}">
        `;
        
        // Para deletar todas as cidades do país
        if (type === 'all_cities') {
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_${type}">
                <input type="hidden" name="id_pais" value="${id}">
            `;
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

// ✏️ EDITAR

function editCountry(id) {
    console.log('Editando país ID:', id); // Debug
    
    fetch(`?action=get_country&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição');
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos:', data); // Debug
            
            if (data.success && data.pais) {
                const pais = data.pais;
                
                // Preencher o formulário de edição
                document.getElementById('edit_id').value = pais.id_pais;
                document.getElementById('edit_nome').value = pais.nome;
                document.getElementById('edit_continente').value = pais.continente;
                document.getElementById('edit_populacao').value = pais.populacao;
                document.getElementById('edit_idioma').value = pais.idioma;
                
                // Abrir o modal
                openModal('editCountryModal');
            } else {
                alert('❌ Erro: Não foi possível carregar os dados do país');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar país:', error);
            alert('❌ Erro ao carregar dados do país. Verifique o console.');
        });
}

function editCity(id) {
    console.log('Editando cidade ID:', id); // Debug
    
    fetch(`?action=get_city&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição');
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos:', data); // Debug
            
            if (data.success && data.cidade) {
                const cidade = data.cidade;
                
                // Preencher o formulário de edição
                document.getElementById('edit_city_id').value = cidade.id_cidade;
                document.getElementById('edit_city_nome').value = cidade.nome;
                document.getElementById('edit_city_populacao').value = cidade.populacao;
                
                // Abrir o modal
                openModal('editCityModal');
            } else {
                alert('❌ Erro: Não foi possível carregar os dados da cidade');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar cidade:', error);
            alert('❌ Erro ao carregar dados da cidade. Verifique o console.');
        });
}

// DEBUG: Verificar se as funções estão carregando
console.log('✅ Script.js carregado corretamente');
console.log('Funções disponíveis:', {
    openModal: typeof openModal,
    closeModal: typeof closeModal,
    editCountry: typeof editCountry,
    editCity: typeof editCity
});