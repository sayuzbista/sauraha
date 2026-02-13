<template>
  <div class="home-container">
    <!-- Navbar -->
    <Navbar />

    <!-- Hero Section with Image -->
    <section class="hero">
      <img :src="heroImage" alt="Tiger" class="hero-image" />
      <div class="hero-overlay">
        <div class="hero-content">
          <h1>Explore Adventures in Sauraha</h1>
          <p>Book unforgettable experiences</p>
          <button class="explore-btn" @click="scrollToServices">Explore Now</button>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section class="services-section" ref="servicesSection">
      <h2 class="section-title">Our Adventures</h2>
      <div class="services-grid">
        <div
          v-for="service in services"
          :key="service.id"
          class="service-card"
          @click="goToService(service.type)"
        >
          <img
            v-if="service.image"
            :src="getImageUrl(service.image)"
            class="service-img"
          />
          <h3>{{ service.name }}</h3>
          <p>{{ service.description }}</p>
          <p class="price">Rs. {{ service.price }}</p>
          <button class="book-btn" @click.stop="handleBooking(service.id)">Book Now</button>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../../axios'
import Navbar from './Navbar.vue'
import Footer from './Footer.vue'
import heroImg from '@/assets/tiger.png' // use download.jpg

const heroImage = heroImg
const router = useRouter()
const servicesSection = ref(null)
const services = ref([])

// Fetch services from backend
const fetchServices = async () => {
  try {
    const res = await axios.get('/admin/services')
    services.value = res.data
  } catch (err) {
    console.error('Error fetching services:', err)
  }
}

// Helper to get image URL
const getImageUrl = (imagePath) => `http://localhost:8000/storage/${imagePath}`

// Scroll to services section
const scrollToServices = () => {
  servicesSection.value.scrollIntoView({ behavior: 'smooth' })
}

// Handle booking button click
const handleBooking = (serviceId) => {
   router.push(`/services/${serviceId}`)
}

// Go to service page
const goToService = (type) => {
  router.push(`/services/${type}`)
}

onMounted(() => {
  fetchServices()
})
</script>

<style scoped>
.home-container {
  font-family: 'Poppins', sans-serif;
  color: white;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* HERO */
.hero {
  position: relative;
  height: 70vh;
  overflow: hidden;
}

.hero-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(0.6);
  position: absolute;
  top: 0;
  left: 0;
  z-index: 0;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1;
}

.hero-content {
  text-align: center;
  color: white;
}

.hero-content h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #22c55e;
}

.hero-content p {
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
}

.explore-btn {
  padding: 0.75rem 1.5rem;
  background-color: #22c55e;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}

.explore-btn:hover {
  background-color: #16a34a;
}

/* SERVICES */
.services-section {
  padding: 4rem 2rem;
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
  background: #1f2937;
  padding: 1rem;
  border-radius: 14px;
  text-align: center;
  transition: transform 0.3s;
  cursor: pointer;
}

.service-card:hover {
  transform: translateY(-5px);
  background: #2563eb;
}

.service-img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 10px;
  margin-bottom: 1rem;
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
  transition: 0.3s;
  color: white;
  font-weight: 600;
}

.book-btn:hover {
  background-color: #2563eb;
}
</style>
