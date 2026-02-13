<template>
  <div>
    <Navbar/>
    <div class="service-requests-page">
      <h1>Pending Vendor Service Requests</h1>
  
      <div v-if="loading" class="loading">Loading requests...</div>
      <div v-else-if="error" class="error">{{ error }}</div>
  
      <div v-else>
        <div v-if="requests.length === 0" class="no-requests">
          No pending service requests.
        </div>
  
        <div class="requests-grid">
          <div v-for="request in requests" :key="request.id" class="request-card">
            <!-- Vendor Info -->
            <div class="vendor-info">
              <h3>{{ request.vendor.company_name }}</h3>
              <p>Email: {{ request.vendor.email }}</p>
            </div>
  
            <!-- Service Info -->
            <div class="service-info">
              <h4>no of vehicles offered: {{ request.title }}</h4>
              <p><strong>Category:</strong> {{ request.category }}</p>
              <p><strong>Description:</strong> {{ request.description }}</p>
              <p><strong>Duration:</strong> {{ request.duration_hours }} hours</p>
              <p><strong>Price:</strong> Rs.{{ request.price }}</p>
              <p><strong>Max Persons:</strong> {{ request.maxPersons || '-' }}</p>
              <p><strong>Offer:</strong> {{ request.offer_percentage || 0 }}%</p>
              <p><strong>Free Person Offer:</strong> {{ request.free_person_offer || '-' }}</p>
              <p><strong>Availability:</strong> {{ request.availability }}</p>
              <p><strong>Promotion Category:</strong> {{ request.promotion_category }}</p>
            </div>
  
            <!-- Service Images -->
            <div class="images-section">
              <h5>Service Images</h5>
              <div class="image-row">
                <img v-for="(img, i) in request.images" :key="i" :src="getImageUrl(img)" />
              </div>
            </div>
  
            <!-- Payment Screenshot -->
            <div v-if="request.promotion_category === 'Gold' || request.promotion_category === 'Premium'" class="payment-screenshot">
              <h5>Payment Screenshot</h5>
              <img :src="getImageUrl(request.payment_screenshot)" />
            </div>
  
            <!-- Action Buttons -->
            <div class="action-buttons">
              <button @click="approve(request.id)">Approve</button>
              <button @click="showRejectReason(request.id)">Reject</button>
            </div>
  
            <!-- Reject Reason Modal -->
            <div v-if="rejectModal.id === request.id" class="modal-overlay">
              <div class="modal">
                <h4>Provide Reason for Rejection</h4>
                <textarea v-model="rejectModal.reason" placeholder="Enter reason..." />
                <div class="modal-buttons">
                  <button @click="reject(request.id)">Submit</button>
                  <button @click="closeRejectModal">Cancel</button>
                </div>
              </div>
            </div>
  
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
  
  const requests = ref([])
  const loading = ref(true)
  const error = ref(null)
  const rejectModal = ref({ id: null, reason: '' })
  
  const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: { Authorization: `Bearer ${localStorage.getItem('adminToken')}` }
  })
  
  // Fetch pending service requests
  const loadRequests = async () => {
    loading.value = true
    try {
      const res = await api.get('/admin/services/pending') // backend endpoint returns pending services with vendor info
      requests.value = res.data
    } catch (err) {
      error.value = 'Failed to load service requests.'
    } finally {
      loading.value = false
    }
  }
  
  // Build image URL
  const getImageUrl = (path) => {
    if (!path) return ''
    if (path.startsWith('http')) return path
    return 'http://localhost:8000' + path
  }
  
  // Approve service
  const approve = async (id) => {
    try {
      await api.post(`/admin/services/${id}/approve`)
      alert('Service approved successfully')
      await loadRequests()
    } catch (err) {
      alert('Failed to approve service')
    }
  }
  
  // Show reject modal
  const showRejectReason = (id) => {
    rejectModal.value = { id, reason: '' }
  }
  
  // Close reject modal
  const closeRejectModal = () => {
    rejectModal.value = { id: null, reason: '' }
  }
  
  // Reject service with reason
  const reject = async (id) => {
    if (!rejectModal.value.reason) {
      alert('Please provide a reason for rejection')
      return
    }
  
    try {
      await api.post(`/admin/services/${id}/reject`, { reason: rejectModal.value.reason })
      alert('Service rejected successfully')
      closeRejectModal()
      await loadRequests()
    } catch (err) {
      alert('Failed to reject service')
    }
  }
  
  onMounted(() => loadRequests())
  </script>
  
  <style scoped>
  .service-requests-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
    color: #e8f5e9;
  }
  h1 {
    text-align: center;
    color: #3b82f6;
    margin-bottom: 30px;
  }
  .loading, .error, .no-requests {
    text-align: center;
    font-size: 18px;
    margin-top: 20px;
  }
  
  .requests-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
  }
  
  .request-card {
    background: #102a1f;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.5);
  }
  
  .vendor-info h3 {
    color: #4caf50;
    margin-bottom: 5px;
  }
  
  .service-info p {
    margin: 4px 0;
  }
  
  .images-section .image-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }
  
  .images-section img, .payment-screenshot img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
  }
  
  .action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
  }
  
  .action-buttons button {
    padding: 8px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
  }
  
  .action-buttons button:first-child {
    background-color: #4caf50;
    color: #fff;
  }
  
  .action-buttons button:last-child {
    background-color: #f44336;
    color: #fff;
  }
  
  /* Modal */
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
  }
  
  .modal {
    background: #102a1f;
    padding: 20px;
    border-radius: 12px;
    width: 400px;
  }
  
  .modal textarea {
    width: 100%;
    min-height: 80px;
    padding: 10px;
    border-radius: 8px;
    border: none;
  }
  
  .modal-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 10px;
  }
  
  .modal-buttons button:first-child {
    background-color: #f44336;
    color: #fff;
    padding: 6px 12px;
    border: none;
    border-radius: 8px;
  }
  
  .modal-buttons button:last-child {
    background-color: #888;
    color: #fff;
    padding: 6px 12px;
    border: none;
    border-radius: 8px;
  }
  </style>
  