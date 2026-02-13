<template>
  <div class="booking-page">
    <Navbar />

    <div class="container">
      <button class="back-btn" @click="$router.back()">← Back to Services</button>

      <div v-if="service" class="service-detail">
        <!-- Service Images -->
        <div class="images">
          <img v-for="(img, i) in service.images" :key="i" :src="img" />
        </div>

        <!-- Service Info -->
        <div class="info">
          <h2 class="green">{{ service.category }}</h2>
          <p>{{ service.description }}</p>
          <p class="price">💰 ₹{{ service.price }} per person</p>
          <p>Max persons per unit: {{ service.maxPersons }}</p>
          <p>Total units available: {{ service.title }}</p>
        </div>

        <!-- Vendor Info -->
        <div class="vendor-info">
          <h3 class="green">Service Provider</h3>
          <p><strong>Company:</strong> {{ service.vendor.company_name }}</p>
          <p><strong>Location:</strong> {{ service.vendor.location }}</p>
          <p>
            <strong>Contact:</strong>
            {{ service.vendor.phone_number }} / {{ service.vendor.email }}
          </p>
          <p>
            <strong>Status:</strong>
            <span class="green">Currently Open</span>
          </p>
          <p>
            <strong>Opening Time:</strong> {{ service.vendor.opening_time }}
            |
            <strong>Closing Time:</strong> {{ service.vendor.closing_time }}
          </p>
        </div>

        <!-- Booking Form -->
        <div class="booking-form">
          <h3 class="green">Book This Service</h3>

          <form @submit.prevent="submitBooking">
            <label>Number of Persons</label>
            <input
              type="number"
              v-model.number="form.persons"
              min="1"
              required
            />
            <div v-if="form.persons > maxPersonsAvailable" class="warning">
              Maximum allowed persons: {{ maxPersonsAvailable }}
            </div>

            <label>Booking Date</label>
            <input type="date" v-model="form.date" :min="today" required />

            <label>Time Slot</label>
            <select v-model="form.time_slot" required>
              <option v-for="slot in availableSlots" :key="slot" :value="slot">
                {{ slot }}
              </option>
            </select>

            <!-- PRICE INFO -->
            <div v-if="service" class="price-info">
              <p>
                Vehicles required:
                <strong>{{ vehiclesRequired }}</strong>
              </p>
              <p>
                Charged persons:
                <strong>{{ chargedPersons }}</strong>
              </p>
              <p>
                Total Price:
                <strong>₹{{ totalPrice }}</strong>
              </p>
            </div>

            <button type="submit">Confirm Booking</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Booking Confirmation Modal -->
    <div v-if="showModal" class="modal">
      <div class="modal-card">
        <h3 class="green">Booking Confirmation</h3>
        <p><strong>Service:</strong> {{ service.category }}</p>
        <p><strong>Persons:</strong> {{ form.persons }}</p>
        <p><strong>Date:</strong> {{ form.date }}</p>
        <p><strong>Time Slot:</strong> {{ form.time_slot }}</p>
        <p class="green">
          <strong>Total Price:</strong> ₹{{ totalPrice }}
        </p>
        <button @click="closeModal">Close</button>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Navbar from './Navbar.vue'
import Footer from './Footer.vue'

const route = useRoute()
const service = ref(null)
const today = new Date().toISOString().split('T')[0]

const form = ref({
  persons: 1,
  date: '',
  time_slot: ''
})

const availableSlots = ref([])
const maxPersonsAvailable = ref(1)
const showModal = ref(false)

const api = axios.create({
  baseURL: 'http://localhost:8000/api'
})

// Load service
const loadService = async () => {
  const res = await api.get(`/service/${route.params.id}`)
  service.value = res.data

  // Time slots
  if (service.value.category.includes('Full Day')) {
    availableSlots.value = ['09:00 - 17:00']
  } else if (service.value.category.includes('Half Day')) {
    availableSlots.value = ['09:00 - 13:00', '13:00 - 17:00']
  } else {
    const startH = parseInt(service.value.vendor.opening_time.split(':')[0])
    const startM = parseInt(service.value.vendor.opening_time.split(':')[1])
    const endH = parseInt(service.value.vendor.closing_time.split(':')[0])
    const endM = parseInt(service.value.vendor.closing_time.split(':')[1])
    const duration = parseInt(service.value.duration_hours)
    const slots = []
    let current = startH + startM / 60
    const endTime = endH + endM / 60
    while (current + duration <= endTime) {
      const startHour = Math.floor(current)
      const startMin = Math.round((current - startHour) * 60)
      const endSlot = current + duration
      const endHour = Math.floor(endSlot)
      const endMin = Math.round((endSlot - endHour) * 60)
      const format = (h, m) =>
        `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`
      slots.push(`${format(startHour, startMin)} - ${format(endHour, endMin)}`)
      current += duration
    }
    availableSlots.value = slots
  }
}

// Vehicles required
const vehiclesRequired = computed(() => {
  if (!service.value) return 0
  return Math.ceil(form.value.persons / service.value.maxPersons)
})

// Charged persons (full units only)
const chargedPersons = computed(() => {
  if (!service.value) return 0
  return vehiclesRequired.value * service.value.maxPersons
})

// Total price
const totalPrice = computed(() => {
  if (!service.value) return 0
  return chargedPersons.value * service.value.price
})

// Watch number of persons to update max capacity
watch(
  () => form.persons,
  () => {
    if (!service.value) return
    maxPersonsAvailable.value = service.value.title * service.value.maxPersons
    if (form.value.persons > maxPersonsAvailable.value) {
      // Do not block input, just show warning
    }
  },
  { immediate: true }
)

// Submit booking (frontend only)
const submitBooking = () => {
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

onMounted(loadService)
</script>

<style scoped>
.booking-page {
  background: #f0f0f0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.container {
  padding: 30px 50px;
  flex: 1;
}

.back-btn {
  margin-bottom: 20px;
  padding: 8px 15px;
  background: #ccc;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.service-detail {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 30px;
}

.images img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 10px;
}

.price {
  font-weight: bold;
  color: #2e7d32;
}

.vendor-info {
  margin-top: 20px;
  padding: 15px;
  background: #f0f8f5;
  border-radius: 10px;
}

.booking-form {
  margin-top: 20px;
  padding: 15px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
}

.booking-form form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.booking-form input,
.booking-form select {
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.booking-form button {
  padding: 12px;
  background: #2e7d32;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.price-info {
  background: #e8f5e9;
  padding: 10px;
  border-radius: 6px;
  color: #2e7d32;
  font-weight: bold;
}

.warning {
  background: #ffebee;
  padding: 10px;
  border-radius: 6px;
  color: #d32f2f;
  font-weight: bold;
}

.green {
  color: #2e7d32;
  font-weight: bold;
}

.modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-card {
  background: #f0fff0;
  padding: 25px;
  border-radius: 12px;
  width: 350px;
  color: #2e7d32;
}

.modal-card button {
  margin-top: 15px;
  padding: 10px;
  background: #2e7d32;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
</style>
