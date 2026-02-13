<template>
  <div>
    <Navbar />
    <div class="vendor-requests-wrapper">
      <h2>Approved Vendors</h2>

      <div v-if="loading" class="loading">Loading...</div>
      <div v-if="error" class="error-msg">{{ error }}</div>

      <div v-if="!loading && vendors.length === 0">
        <p>No approved vendors yet.</p>
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
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from './Navbar.vue'
import axios from 'axios'

const vendors = ref([])
const loading = ref(false)
const error = ref('')

const fetchApprovedVendors = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('http://localhost:8000/api/admin/vendors/approved')
    vendors.value = res.data
  } catch(err) {
    console.error(err)
    error.value = 'Failed to fetch approved vendors.'
  } finally {
    loading.value = false
  }
}

const formatServices = (servicesJson) => {
  try {
    const arr = JSON.parse(servicesJson)
    return arr.join(', ')
  } catch {
    return servicesJson
  }
}

const getFileUrl = (path) => `http://localhost:8000/storage/${path}`

onMounted(fetchApprovedVendors)
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

.vendor-card a {
  color: white;
  text-decoration: underline;
}

.vendor-card a:hover { color: #22c55e; }
</style>
