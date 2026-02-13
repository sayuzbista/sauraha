<template>
  <div class="layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar">🦏</div>
        <h3>{{ vendorName }}</h3>
        <p>Vendor Admin Panel</p>
      </div>

      <nav>
        <button :class="{ active: tab === 'add' }" @click="tab = 'add'">➕ Add Service</button>
        <button :class="{ active: tab === 'list' }" @click="tab = 'list'">📋 Manage Services</button>
      </nav>

      <button class="logout" @click="logout">🚪 Logout</button>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="content">
      <!-- ADD SERVICE -->
      <section v-if="tab === 'add'" class="fade add-section">
        <h2>Add New Adventure</h2>

        <form class="form-card" @submit.prevent="addService">
          <input v-model="form.title" placeholder="No of vehicles/boat/elephant you offer (e.g:3)" required />

          <select v-model="form.category">
            <option>Jeep Safari (Full Day)</option>
            <option>Jeep Safari (Half Day)</option>
            <option>Short / Buffer Zone Safari</option>
            <option>Quad Bike Ride</option>
            <option>Canoeing</option>
            <option>Elephant Ride</option>
            <option>Jungle Walk</option>
          </select>

          <input type="number" v-model="form.price" placeholder="Price per person" required />
          <input type="number" v-model="form.duration_hours" placeholder="Duration (hours)" required />
          <input type="number" v-model="form.maxPersons" placeholder="Maximum Persons" />
          <input v-model="form.offer_percentage" placeholder="Offer (%)" />
          <input v-model="form.free_person_offer" placeholder="Free Person Offer (e.g. 1 free)" />
          <textarea v-model="form.description" placeholder="Service (location) Description"></textarea>

          <select v-model="form.availability">
            <option>Available</option>
            <option>Not Available</option>
          </select>

          <!-- Promotion Category -->
           <div>service visibility to user:</div>
          <select v-model="form.promotion_category" required>
            <option value="Premium">Premium (₹1000)</option>
            <option value="Gold">Gold (₹500)</option>
            <option value="Silver">Silver (Free)</option>
          </select>

          <!-- Admin QR Code Display -->
          <div class="image-row">
            <p>Scan QR to Pay:</p>
            <img :src="qrCodeImage" class="service-img" />
          </div>

          <!-- Payment Screenshot -->
          <div v-if="form.promotion_category === 'Premium' || form.promotion_category === 'Gold'" class="form-group">
            <label>Payment Screenshot</label>
            <input type="file" accept="image/*" @change="handlePaymentScreenshot" />
          </div>

          <div class="form-group">
            <label>Adventure Images (Max 4)</label>
            <input type="file" multiple accept="image/*" @change="handleImages" />
          </div>

          <button type="submit">Add Service</button>
        </form>
      </section>

      <!-- LIST SERVICES -->
      <section v-if="tab === 'list'" class="fade">
        <h2>Your Services</h2>

        <div class="grid">
          <div class="service-card" v-for="s in services" :key="s.id">
            <h3>{{ s.title }}</h3>
            <p>{{ s.category }}</p>
            <p>💰 ₹{{ s.price }} per person</p>
            <p>⏱ Duration: {{ s.duration_hours }} hours</p>
            <p>👥 Max: {{ s.maxPersons }}</p>
            <p>🎁 Offer: {{ s.offer_percentage ? s.offer_percentage+'%' : 'No Offer' }} {{ s.free_person_offer ? ' + '+s.free_person_offer : '' }}</p>
            <p>{{ s.description }}</p>
            <p>🏷 Promotion: {{ s.promotion_category }}</p>
            <p :class="s.status === 'Pending' ? 'black-text' : 'green'">Status: {{ s.status }}</p>
            <p :class="s.availability === 'Available' ? 'black-text' : 'red'">{{ s.availability }}</p>

            <div class="image-row">
              <img v-for="(img, i) in s.images" :key="i" :src="img" class="service-img" />
            </div>

            <!-- Payment Screenshot -->
            <div v-if="s.payment_screenshot" class="image-row">
              <p>Payment Screenshot:</p>
              <img :src="s.payment_screenshot" class="service-img" />
            </div>

            <!-- Admin QR Code -->
            <div class="image-row">
              <p>Scan QR to Pay:</p>
              <img :src="qrCodeImage" class="service-img" />
            </div>

            <div class="actions">
              <button @click="$router.push(`/vendor/service/edit/${s.id}`)">✏️ Edit</button>
              <button class="danger" @click="removeService(s.id)">🗑 Delete</button>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import qrCode from '@/assets/qr.jpg'  // Admin QR code

const router = useRouter()
const tab = ref('add')
const services = ref([])
const vendorName = ref('')
const qrCodeImage = qrCode

// Axios instance with vendor token
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: { Authorization: `Bearer ${localStorage.getItem('vendor_token')}` }
})

// Form state
const form = ref({
  title: '',
  category: 'Jeep Safari (Full Day)',
  price: '',
  duration_hours: '',
  maxPersons: '',
  offer_percentage: '',
  free_person_offer: '',
  description: '',
  availability: 'Available',
  images: [],
  promotion_category: 'Silver',
  payment_screenshot: '',
  status: 'Pending'  // default
})

// Load vendor info
const loadVendor = async () => {
  const res = await api.get('/vendor/dashboard')
  vendorName.value = res.data.vendor.company_name
}

// Load services
const loadServices = async () => {
  try {
    const res = await api.get('/vendor/services')
    services.value = res.data
      .map(s => ({
        ...s,
        images: s.images ? s.images.map(img => (img.startsWith('http') ? img : 'http://localhost:8000' + img)) : [],
        status: s.status || 'Pending'
      }))
      .sort((a, b) => {
        const order = { 'Premium': 1, 'Gold': 2, 'Silver': 3 }
        return order[a.promotion_category] - order[b.promotion_category]
      })
  } catch (err) {
    alert('Failed to load services')
  }
}

// Convert images to base64
const convertToBase64 = (file) => new Promise((resolve, reject) => {
  const reader = new FileReader()
  reader.readAsDataURL(file)
  reader.onload = () => resolve(reader.result)
  reader.onerror = error => reject(error)
})

// Handle multiple images
const handleImages = async (e) => {
  const files = Array.from(e.target.files)
  form.value.images = await Promise.all(files.map(f => convertToBase64(f)))
}

// Handle payment screenshot
const handlePaymentScreenshot = async (e) => {
  const file = e.target.files[0]
  if (!file) return
  form.value.payment_screenshot = await convertToBase64(file)
}

// Add new service
const addService = async () => {
  try {
    await api.post('/vendor/services', form.value)
    form.value = {
      title: '',
      category: 'Jeep Safari (Full Day)',
      price: '',
      duration_hours: '',
      maxPersons: '',
      offer_percentage: '',
      free_person_offer: '',
      description: '',
      availability: 'Available',
      images: [],
      promotion_category: 'Silver',
      payment_screenshot: '',
      status: 'Pending'
    }
    tab.value = 'list'
    loadServices()
  } catch (err) {
    alert(err.response?.data?.errors ? JSON.stringify(err.response.data.errors) : 'Failed to add service')
  }
}

// Delete service
const removeService = async (id) => {
  await api.delete(`/vendor/services/${id}`)
  loadServices()
}

// Logout
const logout = () => {
  localStorage.removeItem('vendor_token')
  router.push('/')
}

onMounted(() => {
  loadVendor()
  loadServices()
})
</script>

<style scoped>
.layout { display: flex; min-height: 100vh; background: #0f1f17; color: #e8f5e9; }
.sidebar { width: 260px; background: #000; padding: 25px; display: flex; flex-direction: column; }
.profile { text-align: center; margin-bottom: 30px; }
.avatar { font-size: 50px; }
nav button { width: 100%; margin-bottom: 10px; padding: 12px; background: #1b5e20; border: none; color: white; border-radius: 8px; }
nav button.active { background: #4caf50; color: #000; }
.logout { margin-top: auto; background: #c62828; color: white; padding: 10px; border-radius: 8px; }
.content { flex: 1; padding: 40px; }
.add-section { background: linear-gradient(135deg, #38623c, #1c261d); padding: 50px; border-radius: 24px; }
.form-card { max-width: 900px; margin: auto; background: #fff; padding: 40px; border-radius: 24px; }
input, select, textarea { width: 100%; padding: 12px; margin-bottom: 12px; border-radius: 8px; border: none; }
button { padding: 12px; background: #4caf50; border: none; border-radius: 8px; cursor: pointer; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
.service-card { background: #102a1f; padding: 20px; border-radius: 16px; }
.image-row { display: flex; gap: 8px; margin-top: 10px; align-items: center; }
.service-img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
.actions { display: flex; justify-content: space-between; margin-top: 15px; }
.danger { background: #d32f2f; color: white; }
.modal { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.7); display: flex; align-items: center; justify-content: center; }
.modal-card { background: #1b5e20; padding: 30px; max-height: 90vh;  border-radius: 16px; width: 400px; }
.fade { animation: fadeIn 0.4s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.black-text { color: black; }
.red { color: #ef5350; }
.green { color: #66bb6a; }
</style>
