<template>
    <div>
      <Navbar />
  <div class="vendor-requests-wrapper">
     
    <h2>Vendor Registration Requests</h2>

    <div v-if="loading" class="loading">Loading...</div>
    <div v-if="error" class="error-msg">{{ error }}</div>

    <div v-if="!loading && vendors.length === 0">
      <p>No pending vendor requests.</p>
    </div>

    <div v-else class="vendor-list">
      <div v-for="vendor in vendors" :key="vendor.id" class="vendor-card">
        <h3>{{ vendor.company_name }}</h3>
        <p><strong>Owner:</strong> {{ vendor.owner_name }}</p>
        <p><strong>Email:</strong> {{ vendor.email }}</p>
        <p><strong>Phone:</strong> {{ vendor.phone_number }}</p>
        <p><strong>Location:</strong> {{ vendor.location }}</p>
        <p><strong>PAN:</strong> {{ vendor.pan_number }}</p>
        <p><strong>Services:</strong> {{ formatServices(vendor.services_provided) }}</p>
        <p><strong>Opening:</strong> {{ vendor.opening_time }} - <strong>Closing:</strong> {{ vendor.closing_time }}</p>
        <p>
          <strong>Company Doc:</strong>
          <a :href="getFileUrl(vendor.company_doc)" target="_blank">View</a>
        </p>
        <p>
          <strong>Building Photo:</strong>
          <a :href="getFileUrl(vendor.building_photo)" target="_blank">View</a>
        </p>

        <div class="action-buttons">
          <button @click="approveVendor(vendor.id)">Approve</button>
          <button @click="rejectVendor(vendor.id)" class="reject">Reject</button>
        </div>
      </div>
    </div>
  </div>
  </div>
  
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from './Navbar.vue';
import axios from 'axios'

const vendors = ref([])
const loading = ref(false)
const error = ref('')

// Fetch pending vendors
const fetchVendors = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('http://localhost:8000/api/admin/vendors/pending')
    vendors.value = res.data
  } catch(err) {
    console.error(err)
    error.value = 'Failed to fetch vendor requests.'
  } finally {
    loading.value = false
  }
}

// Approve vendor
const approveVendor = async (id) => {
  try {
    await axios.post(`http://localhost:8000/api/admin/vendors/${id}/approve`)
    vendors.value = vendors.value.filter(v => v.id !== id)
    alert('Vendor approved! Email will be sent automatically.')
  } catch(err) {
    console.error(err)
    alert('Failed to approve vendor.')
  }
}

// Reject vendor
const rejectVendor = async (id) => {
  try {
    await axios.post(`http://localhost:8000/api/admin/vendors/${id}/reject`)
    vendors.value = vendors.value.filter(v => v.id !== id)
    alert('Vendor rejected! Email will be sent automatically.')
  } catch(err) {
    console.error(err)
    alert('Failed to reject vendor.')
  }
}

// Helper: format services
const formatServices = (servicesJson) => {
  try {
    const arr = JSON.parse(servicesJson)
    return arr.join(', ')
  } catch {
    return servicesJson
  }
}

// Helper: file URLs
const getFileUrl = (path) => `http://localhost:8000/storage/${path}`

onMounted(fetchVendors)
</script>

<style scoped>
.vendor-requests-wrapper {
  padding: 20px;
  font-family: 'Poppins', sans-serif;
}

h2 { margin-bottom: 20px; color: #22c55e; }

.loading { color: #3b82f6; }
.error-msg { color: #f87171; margin-bottom: 10px; }

.vendor-list { display: flex; flex-direction: column; gap: 15px; }

.vendor-card {
  background: #1f2937;
  padding: 20px;
  border-radius: 10px;
  color: white;
}

.vendor-card h3 { color: #22c55e; margin-bottom: 10px; }

.action-buttons { margin-top: 10px; display: flex; gap: 10px; }
button {
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  color: white;
}

button:hover { opacity: 0.9; }
button.reject { background: #f87171; }
button:not(.reject) { background: #22c55e; }
</style>
