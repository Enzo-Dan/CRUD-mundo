# 🌍 CRUD Mundo

Aplicação web simples e bonita para gerenciar países e cidades do mundo!

## 📋 Funcionalidades

✅ **Países:** Criar, editar, deletar  
✅ **Cidades:** Adicionar a países, editar, deletar  
✅ **APIs Externas:** REST Countries (bandeira, moeda, capital) + OpenWeatherMap (clima)  
✅ **Design Moderno:** Interface limpa e responsiva  

## 🚀 Instalação Rápida

### 1️⃣ Criar Banco de Dados

```bash
mysql -u root -p < database.sql
```

### 2️⃣ Configurar API (Opcional)

Abra `config.php` e coloque sua chave:

```php
define('API_KEY', 'sua_chave_aqui');
```

Obtenha em: https://openweathermap.org/api

### 3️⃣ Iniciar Servidor

```bash
php -S localhost:8000
```

Acesse: `http://localhost:8000`

## 📁 Estrutura

```
crud-mundo-php/
├── index.php          ← Arquivo principal
├── config.php         ← Configurações (coloque sua API aqui!)
├── functions.php      ← Funções auxiliares
├── database.sql       ← Script do banco de dados
├── style.css          ← Estilos
├── script.js          ← JavaScript
└── README.md          ← Este arquivo
```

## 🎨 Design

- Cores modernas (azul, verde, vermelho)
- Cards responsivos
- Modais interativos
- Animações suaves
- Mobile-friendly

## 🔐 Segurança

✅ Prepared statements (proteção contra SQL Injection)  
✅ Validação de entrada  
✅ Proteção contra XSS  

## 💡 Como Usar

1. Clique em **"+ Adicionar País"**
2. Preencha os dados (bandeira, moeda e capital preenchem automaticamente!)
3. Clique em **"+ Adicionar Cidade"** no card do país
4. Aproveite os dados climáticos em tempo real! 🌡️

## 📝 Notas

- Deixe a chave de API em branco se não quiser dados de clima
- Não é possível deletar país com cidades (delete as cidades primeiro)
- Nomes de países são únicos

---

**Desenvolvido com ❤️ para a disciplina de Desenvolvimento de Sistemas**
