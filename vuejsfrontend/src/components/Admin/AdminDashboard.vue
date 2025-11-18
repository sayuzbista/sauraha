<template>
  <div>
    <Navbar />
    <div class="container">
      <h1 class="title">Admin Dashboard</h1>
      <p>Welcome! You can view data and manage services from the menu above.</p>
    </div>
  </div>
</template>

<script setup>
import Navbar from './Navbar.vue';
</script>

<style scoped>
.container {
  padding: 2rem;
  color: white;
  background-color: #000;
  min-height: 90vh;
}

.title {
  color: #3b82f6;
  font-size: 2rem;
  margin-bottom: 1.5rem;
}
</style>
