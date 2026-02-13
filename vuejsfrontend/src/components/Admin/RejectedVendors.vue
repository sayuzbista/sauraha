<template>
  <div>
    <Navbar />
    <div class="vendor-requests-wrapper">
      <h2>Rejected Vendors</h2>

      <div v-if="loading" class="loading">Loading...</div>
      <div v-if="error" class="error-msg">{{ error }}</div>
      <div v-if="!loading && vendors.length === 0">
        <p>No rejected vendors.</p>
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
            <button class="reject" @click="deleteVendor(vendor.id)">Delete</button>
          </div>
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

// Fetch rejected vendors
const fetchRejectedVendors = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('http://localhost:8000/api/admin/vendors/rejected')
    vendors.value = res.data
  } catch(err) {
    console.error(err)
    error.value = 'Failed to fetch rejected vendors.'
  } finally {
    loading.value = false
  }
}

// Delete vendor
const deleteVendor = async (id) => {
  if (!confirm('Are you sure you want to delete this vendor permanently?')) return
  try {
    await axios.delete(`http://localhost:8000/api/admin/vendors/${id}`)
    vendors.value = vendors.value.filter(v => v.id !== id)
    alert('Vendor deleted successfully.')
  } catch(err) {
    console.error(err)
    alert('Failed to delete vendor.')
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

onMounted(fetchRejectedVendors)
</script>

<style scoped>
.vendor-requests-wrapper {
  padding: 20px;
  font-family: 'Poppins', sans-serif;
}

h2 { 
  margin-bottom: 20px; 
  color: #f87171; /* Heading red */
}

.loading { color: #3b82f6; }
.error-msg { color: #f87171; margin-bottom: 10px; }

.vendor-list { display: flex; flex-direction: column; gap: 15px; }

.vendor-card {
  background: #1f2937;
  padding: 20px;
  border-radius: 10px;
  color: #f87171; /* All text red */
}

.vendor-card h3 { 
  color: #f87171; /* Heading red */ 
  margin-bottom: 10px; 
}

.vendor-card a {
  color: #f87171; /* Links red */
  text-decoration: underline;
}

.vendor-card a:hover { 
  color: #fca5a5; /* Slight lighter red on hover */
}

.action-buttons {
  margin-top: 10px;
  display: flex;
  gap: 10px;
}

button {
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  color: white; /* Keep delete button text white */
}

button.reject {
  background: #f87171; /* Red background */
}

button:not(.reject) {
  background: #22c55e;
}
</style>
