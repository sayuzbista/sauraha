<template>
  <nav class="navbar">
    <!-- Logo -->
    <div class="logo" @click="goHome">Ghumau Sauraha</div>

    <!-- Menu Links -->
    <ul class="nav-links">
      <li><a @click.prevent="goHome">Home</a></li>
      <li><a @click.prevent="goServices">Services</a></li>
      <li><a @click.prevent="goPackages">Packages</a></li>
      <li><a @click.prevent="goVendors">Vendors</a></li>
      <li><a @click.prevent="goAbout">About Us</a></li>
      <li><a @click.prevent="goContact">Contact</a></li>
    </ul>

    <!-- Search and Profile -->
    <div class="search-profile">
      <div class="search-bar">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search services..."
          @keyup.enter="performSearch"
        />
        <button @click="performSearch"><i class="fa-solid fa-magnifying-glass"></i></button>
      </div>

      <div class="profile-dropdown" @click="toggleDropdown">
        <i class="fa-solid fa-user"></i>
        <ul v-if="dropdownOpen" class="dropdown-menu">
          <li><a @click.prevent="goProfile">My Profile</a></li>
          <li><a @click.prevent="goBookings">My Bookings</a></li>
          <li><a @click.prevent="logout">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const searchQuery = ref('');
const dropdownOpen = ref(false);

const toggleDropdown = () => (dropdownOpen.value = !dropdownOpen.value);

const goHome = () => router.push({ name: 'Home' });
const goServices = () => router.push({ path: '/services/canoeing' });
const goPackages = () => router.push({ name: 'Packages' });
const goVendors = () => router.push({ name: 'ApprovedVendors' });
const goAbout = () => router.push({ name: 'About' });
const goContact = () => router.push({ name: 'Contact' });
const goProfile = () => router.push({ name: 'UserProfile' });
const goBookings = () => router.push({ name: 'MyBookings' });

const performSearch = () => {
  if (!searchQuery.value.trim()) return;
  router.push({ name: 'SearchResults', query: { q: searchQuery.value } });
};

const logout = () => {
  localStorage.removeItem('token');
  router.push({ name: 'Landing' });
};
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  background-color: #111827;
  color: white;
  border-bottom: 2px solid #1f2937;
  position: sticky;
  top: 0;
  z-index: 100;
}

.logo {
  font-size: 1.8rem;
  font-weight: bold;
  cursor: pointer;
  color: #22c55e;
}

.nav-links {
  display: flex;
  gap: 1.5rem;
  list-style: none;
}

.nav-links li a {
  color: white;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.3s;
}

.nav-links li a:hover {
  color: #22c55e;
}

.search-profile {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-bar {
  display: flex;
  border-radius: 50px;
  overflow: hidden;
  border: 1px solid #374151;
  background-color: #1f2937;
}

.search-bar input {
  padding: 0.5rem 1rem;
  border: none;
  outline: none;
  width: 200px;
  color: white;
  background: transparent;
}

.search-bar input::placeholder {
  color: #9ca3af;
}

.search-bar button {
  padding: 0 1rem;
  background-color: #2563eb;
  color: white;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s;
}

.search-bar button:hover {
  background-color: #1e40af;
}

.profile-dropdown {
  position: relative;
  cursor: pointer;
  font-size: 1.4rem;
  color: white;
}

.dropdown-menu {
  position: absolute;
  right: 0;
  top: 2rem;
  background-color: #1f2937;
  border-radius: 10px;
  list-style: none;
  padding: 0.5rem 0;
  width: 160px;
  box-shadow: 0 6px 12px rgba(0,0,0,0.5);
}

.dropdown-menu li a {
  display: block;
  padding: 0.5rem 1rem;
  color: white;
  font-weight: 500;
  text-decoration: none;
  transition: background-color 0.3s;
}

.dropdown-menu li a:hover {
  background-color: #2563eb;
  border-radius: 6px;
}
</style>
