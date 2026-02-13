<template>
  <nav class="navbar">
    <div class="logo">Admin Panel</div>

    <ul class="nav-links">
      <li @click="$router.push('/admin/dashboard')">Dashboard</li>
      <li @click="$router.push('/admin/services')">Services</li>
      <li @click="$router.push('/admin/service-requests')">Service Requests</li>


      <!-- Vendor Info Dropdown -->
      <li class="dropdown">
        <span>Vendor Info ▾</span>
        <ul class="dropdown-menu">
          <li @click="$router.push('/admin/vendors')">
            Vendor Requests
          </li>
          <li @click="$router.push('/admin/vendors/accepted')">
            Accepted Vendors
          </li>
          <li @click="$router.push('/admin/vendors/rejected')">
            Rejected Vendors
          </li>
        </ul>
      </li>

      <li @click="logout">Logout</li>
    </ul>
  </nav>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()

const logout = () => {
  localStorage.removeItem('adminToken')
  router.push('/')
}
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
}

.nav-links li:hover {
  color: #3b82f6;
}

/* Dropdown */
.dropdown-menu {
  display: none;
  position: absolute;
  top: 120%;
  left: 0;
  background: #111827;
  border-radius: 6px;
  min-width: 180px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.4);
  z-index: 100;
}

.dropdown-menu li {
  padding: 10px 15px;
  white-space: nowrap;
}

.dropdown-menu li:hover {
  background: #1f2937;
  color: #3b82f6;
}

.dropdown:hover .dropdown-menu {
  display: block;
}
</style>
