<template>
  <div class="edit-page">
    <h2>Edit Service</h2>

    <div v-if="loading">Loading service...</div>
    <div v-else-if="error">{{ error }}</div>

    <form v-else @submit.prevent="updateService">

      <!-- Number of vehicles (title) -->
      <input v-model="service.title" placeholder="No of vehicles/boat/elephant you offer (e.g:3)" required />

      <!-- Category -->
      <select v-model="service.category">
        <option>Jeep Safari (Full Day)</option>
        <option>Jeep Safari (Half Day)</option>
        <option>Short / Buffer Zone Safari</option>
        <option>Quad Bike Ride</option>
        <option>Canoeing</option>
        <option>Elephant Ride</option>
        <option>Jungle Walk</option>
      </select>

      <!-- Price & Duration -->
      <input type="number" v-model="service.price" placeholder="Price per person" required />
      <input type="number" v-model="service.duration_hours" placeholder="Duration (hours)" required />

      <!-- Max persons, offers -->
      <input type="number" v-model="service.maxPersons" placeholder="Max Persons" />
      <input v-model="service.offer_percentage" placeholder="Offer %" />
      <input v-model="service.free_person_offer" placeholder="Free Person Offer" />

      <!-- Description -->
      <textarea v-model="service.description" placeholder="Description"></textarea>

      <!-- Availability -->
      <select v-model="service.availability">
        <option>Available</option>
        <option>Not Available</option>
      </select>

      <!-- Promotion Category -->
      <select v-model="service.promotion_category" @change="handlePromotionChange">
        <option>Silver</option>
        <option>Gold</option>
        <option>Premium</option>
      </select>

      <!-- Payment Screenshot (Gold or Premium only) -->
      <div v-if="service.promotion_category === 'Gold' || service.promotion_category === 'Premium'">
        <label>Payment Screenshot</label>
        <div v-if="existingPaymentScreenshot">
          <img :src="getImageUrl(existingPaymentScreenshot)" class="service-img" />
        </div>
        <input type="file" accept="image/*" @change="handlePaymentScreenshot" />
      </div>

      <!-- Existing Service Images -->
      <div class="image-section">
        <label>Existing Service Images</label>
        <div class="image-row">
          <img v-for="(img, index) in service.images" :key="index" :src="getImageUrl(img)" class="service-img" />
        </div>
      </div>

      <!-- Replace Service Images -->
      <div class="form-group">
        <label>Replace Service Images (optional)</label>
        <input type="file" multiple accept="image/*" @change="handleImages" />
      </div>

      <button type="submit">Update Service</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const service = ref({})
const images = ref([]) // new service images
const paymentScreenshot = ref(null) // new payment screenshot
const existingPaymentScreenshot = ref(null)
const loading = ref(true)
const error = ref(null)

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: { Authorization: `Bearer ${localStorage.getItem('vendor_token')}` }
})

// Load service data
const loadService = async () => {
  try {
    const res = await api.get(`/vendor/services/${route.params.id}`)
    service.value = res.data
    existingPaymentScreenshot.value = res.data.payment_screenshot || null
    loading.value = false
  } catch (err) {
    error.value = 'Failed to load service'
    loading.value = false
  }
}

// Convert file to base64
const convertToBase64 = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = () => resolve(reader.result)
    reader.onerror = err => reject(err)
  })
}

// Handle new service images
const handleImages = async (e) => {
  const files = Array.from(e.target.files)
  const base64Images = await Promise.all(files.map(f => convertToBase64(f)))
  images.value = base64Images
}

// Handle payment screenshot
const handlePaymentScreenshot = async (e) => {
  const file = e.target.files[0]
  if (file) {
    paymentScreenshot.value = await convertToBase64(file)
  }
}

// Build full URL
const getImageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return 'http://localhost:8000' + path
}

// Handle promotion category change
const handlePromotionChange = () => {
  if (service.value.promotion_category === 'Silver') {
    paymentScreenshot.value = null
  }
}

// Update service
const updateService = async () => {
  try {
    const payload = {
      title: String(service.value.title),  
      category: service.value.category,
      price: service.value.price,
      duration_hours: service.value.duration_hours,
      maxPersons: service.value.maxPersons,
      offer_percentage: service.value.offer_percentage,
      free_person_offer: service.value.free_person_offer,
      description: service.value.description,
      availability: service.value.availability,
      promotion_category: service.value.promotion_category
    }

    // Include new images if any
    if (images.value.length > 0) {
      payload.images = images.value
    }

    // Include payment screenshot if applicable
    if ((service.value.promotion_category === 'Gold' || service.value.promotion_category === 'Premium') && paymentScreenshot.value) {
      payload.payment_screenshot = paymentScreenshot.value
    }

    await api.put(`/vendor/services/${route.params.id}`, payload)

    // Clear new images after success
    images.value = []
    paymentScreenshot.value = null

    alert('Service updated successfully')
    router.replace('/vendor/dashboard')
  } catch (err) {
    alert(err.response?.data?.errors ? JSON.stringify(err.response.data.errors) : 'Update failed')
  }
}

onMounted(() => loadService())
</script>

<style scoped>
.edit-page {
  max-width: 900px;
  margin: 40px auto;
  padding: 30px;
  background: #102a1f;
  color: #e8f5e9;
  border-radius: 20px;
}
input, select, textarea {
  width: 100%;
  padding: 12px;
  margin-bottom: 12px;
  border-radius: 8px;
  border: none;
}
button {
  padding: 12px;
  background: #4caf50;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
.image-row {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}
.service-img {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 8px;
}
.image-section {
  margin-bottom: 20px;
}
</style>
