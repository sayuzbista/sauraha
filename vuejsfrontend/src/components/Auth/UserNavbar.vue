<template>
  <div class="landing-container">

    <!-- PUBLIC NAVBAR -->
    <nav class="navbar">
      <div class="logo">Ghumau Sauraha</div>
      <div class="nav-links">
        <a href="#home">Home</a>
        <a href="#services">Services</a>
        <a href="#packages">Packages</a>
        <a href="#about">About Us</a>
        <a href="#contact">Contact</a>
      </div>
      <div class="nav-actions">
        <router-link to="/login" class="btn-login">Login</router-link>
        <router-link to="/register" class="btn-register">Register</router-link>
      </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
      <div class="hero-left">
        <h1>Discover <span>Ghumau Sauraha</span></h1>
        <p>Experience adventure, culture & unforgettable moments in Chitwan.</p>

        <!-- BIG SEARCH BAR -->
        <div class="search-bar">
          <input v-model="searchQuery" type="text" placeholder="Search for services or packages..." />
          <button @click="search">Search</button>
        </div>

        <!-- HERO OPTIONS -->
        <div class="hero-options">
          <button @click="scrollToSection('services')">Services</button>
          <button @click="scrollToSection('packages')">Packages</button>
          <button @click="scrollToSection('about')">About Us</button>
          <button @click="scrollToSection('contact')">Contact</button>
        </div>
      </div>

      <div class="hero-right">
        
      </div>
    </section>

    <!-- SERVICES SECTION -->
    <section class="services-section" ref="services">
      <h2 class="section-title">Our Services</h2>
      <div class="services-grid">
        <div v-for="service in services" :key="service.id" class="service-card">
          <img v-if="service.image" :src="`http://localhost:8000/storage/${service.image}`" class="service-img" />
          <h3>{{ service.name }}</h3>
          <p>{{ service.description }}</p>
          <p class="price">Rs. {{ service.price }}</p>
          <button class="book-btn" @click="redirectToLogin">Book Now</button>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer" id="contact">
      <div class="footer-top">
        <div class="footer-section">
          <h3>About Ghumau Sauraha</h3>
          <p>Experience the best adventures, culture, and hospitality in Chitwan. Make your trips unforgettable!</p>
        </div>

        <div class="footer-section">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#packages">Packages</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>

        <div class="footer-section">
          <h3>Contact Us</h3>
          <form @submit.prevent="submitEmail">
            <input type="email" v-model="email" placeholder="Enter your email" required />
            <button type="submit">Submit</button>
          </form>
          <p v-if="successMessage" class="success">{{ successMessage }}</p>
        </div>
      </div>

      <div class="footer-bottom">
        &copy; 2025 Ghumau Sauraha. All rights reserved.
      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../../axios';

const services = ref([]);
const searchQuery = ref('');
const servicesSection = ref(null);
const email = ref('');
const successMessage = ref('');
const router = useRouter();

// Fetch services from API
const fetchServices = async () => {
  try {
    const res = await axios.get('/admin/services');
    services.value = res.data;
  } catch (err) {
    console.error(err);
  }
};

// Scroll to section
const scrollToSection = (id) => {
  const el = id === 'services' ? servicesSection.value : document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth' });
};

// Book now redirects to login
const redirectToLogin = () => {
  router.push({ name: 'Login' });
};

// Search (basic placeholder)
const search = () => {
  alert(`Search for: ${searchQuery.value}`);
};

// Footer email submission
const submitEmail = () => {
  successMessage.value = `Thank you! We will contact you at ${email.value}.`;
  email.value = '';
};

onMounted(() => {
  fetchServices();
});
</script>

<style scoped>
/* GENERAL STYLING */
.landing-container {
  font-family: 'Poppins', sans-serif;
  background-color: #f9fafb;
  color: #1f2937;
}

/* NAVBAR */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 4rem;
  background-color: #ffffff;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.nav-links a {
  margin: 0 1rem;
  color: #1f2937;
  text-decoration: none;
  font-weight: 500;
}

.nav-links a:hover {
  color: #22c55e;
}

.nav-actions a {
  margin-left: 1rem;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
}

.btn-login {
  background-color: #3b82f6;
  color: white;
}

.btn-login:hover {
  background-color: #2563eb;
}

.btn-register {
  background-color: #22c55e;
  color: white;
}

.btn-register:hover {
  background-color: #16a34a;
}

/* HERO */
.hero {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 3rem 4rem;
  background: linear-gradient(to right, #e0f7fa, #ffffff);
}

.hero-left h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.hero-left h1 span {
  color: #22c55e;
}

.hero-left p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
}

.search-bar {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.search-bar input {
  flex: 1;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  border: 1px solid #ccc;
}

.search-bar button {
  padding: 0.75rem 1.5rem;
  border: none;
  background-color: #22c55e;
  color: white;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
}

.hero-options button {
  margin-right: 1rem;
  padding: 0.5rem 1.25rem;
  background: #3b82f6;
  border: none;
  border-radius: 8px;
  color: white;
  cursor: pointer;
  font-weight: 500;
  margin-top: 0.5rem;
}

.hero-options button:hover {
  background: #2563eb;
}

.hero-right {
  flex: 1;
  display: flex;
  justify-content: center;
}

.hero-img {
  max-width: 90%;
  animation: float 4s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}

/* SERVICES SECTION */
.services-section {
  padding: 3rem 4rem;
  margin-top: -3rem;
}

.section-title {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 2rem;
  color: #22c55e;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.service-card {
  background: #ffffff;
  padding: 1rem;
  border-radius: 14px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}

.service-card:hover {
  transform: translateY(-5px);
}

.service-img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 10px;
  margin-bottom: 0.5rem;
}

.price {
  margin: 0.5rem 0;
  color: #22c55e;
  font-weight: 600;
}

.book-btn {
  padding: 0.5rem 1.25rem;
  border: none;
  background: #3b82f6;
  border-radius: 8px;
  cursor: pointer;
  color: white;
  font-weight: 500;
}

.book-btn:hover {
  background: #2563eb;
}

/* FOOTER */
.footer {
  background-color: #f3f4f6;
  color: #1f2937;
  padding: 3rem 4rem;
}

.footer-top {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  margin-bottom: 2rem;
}

.footer-section {
  flex: 1 1 250px;
  margin: 1rem;
}

.footer-section h3 {
  color: #22c55e;
  margin-bottom: 1rem;
}

.footer-section a,
.footer-section p,
.footer-section li {
  color: #374151;
}

.footer-section a:hover {
  color: #22c55e;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section form {
  display: flex;
  flex-direction: column;
}

.footer-section input {
  padding: 0.5rem;
  margin-bottom: 0.5rem;
  border-radius: 6px;
  border: 1px solid #d1d5db;
}

.footer-section button {
  padding: 0.5rem;
  border: none;
  border-radius: 6px;
  background-color: #22c55e;
  color: white;
  cursor: pointer;
}

.footer-section button:hover {
  background-color: #16a34a;
}

.success {
  color: #3b82f6;
  margin-top: 0.5rem;
}

.footer-bottom {
  text-align: center;
  border-top: 1px solid #d1d5db;
  padding-top: 1rem;
  font-size: 0.9rem;
  color: #6b7280;
}
</style>
