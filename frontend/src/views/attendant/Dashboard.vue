<template>
  <div class="container dashboard">
    <div class="dashboard-header">
      <h1>Dashboard do Atendente</h1>
      <p>Gerencie clientes e locações</p>
    </div>
    <div class="section">
      <h2 class="section-title">Acesso Rápido</h2>
      <div style="display: flex; gap: 20px;">
        <router-link to="/attendant/clients" class="btn btn-primary">Gerenciar Clientes</router-link>
        <router-link to="/attendant/rentals" class="btn btn-primary">Gerenciar Locações</router-link>
      </div>
    </div>
  </div>
</template>
