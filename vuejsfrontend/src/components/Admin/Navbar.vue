<template>
  <nav class="navbar">
    <div class="logo">Admin Panel</div>
    <ul class="nav-links">
      <li @click="$router.push('/admin/dashboard')">Dashboard</li>
      <li @click="$router.push('/admin/services')">Services</li>
      <li @click="logout">Logout</li>
    </ul>
  </nav>
</template>

<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();

const logout = () => {
  localStorage.removeItem('adminToken');
  router.push('/admin/login');
};
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #1f2937;
  padding: 1rem 3rem;
}

.logo {
  color: #3b82f6;
  font-weight: 700;
  font-size: 1.5rem;
}

.nav-links {
  display: flex;
  gap: 2rem;
  list-style: none;
}

.nav-links li {
  cursor: pointer;
  color: white;
  font-weight: 600;
  position: relative;
  padding-bottom: 0.25rem;
  transition: all 0.3s ease;
}

.nav-links li:hover {
  color: #3b82f6;
}

.nav-links li::after {
  content: '';
  display: block;
  width: 0%;
  height: 2px;
  background: #3b82f6;
  transition: width 0.3s;
  position: absolute;
  bottom: 0;
  left: 0;
}

.nav-links li:hover::after {
  width: 100%;
}
</style>
