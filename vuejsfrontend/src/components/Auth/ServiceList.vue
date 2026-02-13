<template>
  <div class="services-page">
    <Navbar />

    <div class="container">
      <h2>Available Adventures (Fetched {{ services.length }} services)</h2>

      <!-- 🔽 Sorting Dropdown -->
      <div class="sort-container">
        <select v-model="selectedSort" @change="loadServices">
          <option value="">Default (Promotion Priority)</option>
          <option value="min_price">Minimum Price</option>
          <option value="max_price">Maximum Price</option>
        </select>
      </div>

      <div class="grid">
        <div class="service-card" v-for="service in sortedServices" :key="service.id">
          <div class="card-image">
            <img :src="service.images[0] || defaultImage" alt="Service Image" />
            <span :class="['availability-badge', badgeColor(service.promotion_category)]">
              {{ service.promotion_category?.toUpperCase() }}
            </span>
          </div>

          <div class="card-content">
            <h3>{{ service.category }}</h3>
            <p class="vendor-name">
              <strong>Service Provider:</strong> {{ service.vendor.company_name }}
            </p>
            <p class="price">💰 ₹{{ service.price }} per person</p>
            <button class="book-btn" @click="openBooking(service)">
              Book Now
            </button>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import Navbar from './Navbar.vue'
import Footer from './Footer.vue'

const services = ref([])
const selectedSort = ref('')
const router = useRouter()
const defaultImage = 'https://via.placeholder.com/250x160?text=No+Image'

const api = axios.create({ baseURL: 'http://localhost:8000/api' })

const loadServices = async () => {
  try {
    const res = await api.get('/services', {
      params: { sort: selectedSort.value }
    })

    services.value = res.data.map(s => ({
      ...s,
      images: s.images
        ? s.images.map(img =>
            img.startsWith('http') ? img : 'http://localhost:8000' + img
          )
        : []
    }))
  } catch (err) {
    alert('Failed to load services')
  }
}

const openBooking = (service) => {
  router.push({ name: 'ServiceBooking', params: { id: service.id } })
}

// ✅ Badge color based on promotion
const badgeColor = (promotion) => {
  switch (promotion) {
    case 'premium': return 'badge-premium'
    case 'gold': return 'badge-gold'
    case 'silver': return 'badge-silver'
    default: return 'badge-default'
  }
}

// ✅ Computed sorted services (global price sorting)
const sortedServices = computed(() => {
  if (!selectedSort.value || selectedSort.value === '') {
    return services.value // default promotion order
  }

  const sorted = [...services.value]

  if (selectedSort.value === 'min_price') {
    sorted.sort((a, b) => a.price - b.price)
  } else if (selectedSort.value === 'max_price') {
    sorted.sort((a, b) => b.price - a.price)
  }

  return sorted
})

onMounted(loadServices)
</script>

<style scoped>
.services-page { background: #e0e0e0; min-height: 100vh; display: flex; flex-direction: column; }
.container { padding: 30px 50px; flex: 1; }
h2 { color: #1e2a2b; margin-bottom: 25px; font-weight: 600; }

.sort-container { margin-bottom: 20px; }
.sort-container select { padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; }

.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }

.service-card {
  background: linear-gradient(145deg, #ffffff, #e6f2ea);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s, box-shadow 0.2s;
}
.service-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }

.card-image { position: relative; }
.card-image img { width: 100%; height: 180px; object-fit: cover; }

/* Promotion badge colors */
.availability-badge { position: absolute; top: 10px; left: 10px; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; color: white; }
.badge-premium { background-color: #FFD700; } /* Gold */
.badge-gold { background-color: #FF8C00; }     /* Dark Orange */
.badge-silver { background-color: #C0C0C0; }   /* Silver */
.badge-default { background-color: #4CAF50; }  /* Green for others */

.card-content { padding: 15px; display: flex; flex-direction: column; gap: 8px; }
h3 { margin: 0; font-size: 1.15rem; color: #2e7d32; }
.vendor-name { font-size: 0.95rem; color: #1e2a2b; }
.price { font-weight: bold; color: #388e3c; }

.book-btn {
  padding: 10px;
  background: #4caf50;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
.book-btn:hover { background: #388e3c; }
</style>
