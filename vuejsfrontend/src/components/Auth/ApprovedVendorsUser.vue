<template>
  <div class="services-page">
    <Navbar/>
  <div class="vendors-wrapper">
    <h2>Approved Vendors</h2>

    <!-- Loading & Error -->
    <div v-if="loading" class="loading">Loading vendors...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else class="vendors-grid">
      <div class="vendor-card" v-for="vendor in vendors" :key="vendor.id">
        <!-- Building Photo -->
        <div class="photo-container">
          <img
            :src="getBuildingPhoto(vendor.building_photo)"
            alt="Building Photo"
            class="vendor-photo"
          />
        </div>

        <div class="vendor-info">
          <h3>{{ vendor.company_name }}</h3>
          <p><span>Owner:</span> {{ vendor.owner_name }}</p>
          <p><span>Location:</span> {{ vendor.location }}</p>
          <p><span>Email:</span> {{ vendor.email }}</p>
          <p><span>Services:</span> {{ vendor.services_provided }}</p>
          <p><span>Opening and closing timings:</span> {{ vendor.opening_time }} - {{ vendor.closing_time }}</p>
          <p><span>Providing service since:</span> {{ formatDate(vendor.created_at) }}</p>
        </div>
      </div>
    </div>
  </div>
  <Footer/>
  </div>
  
</template>

<script>
import axios from 'axios';
import Navbar from './Navbar.vue'
import Footer from './Footer.vue'
export default {
  name: "ApprovedVendorsUser",

  components: {
    Navbar,
    Footer
  },
  data() {
    return {
      vendors: [],
      loading: false,
      error: ''
    };
  },
  created() {
    this.fetchVendors();
  },
  methods: {
    async fetchVendors() {
      this.loading = true;
      this.error = '';
      try {
        const res = await axios.get('http://localhost:8000/api/user/approved-vendors');
        this.vendors = res.data;
      } catch (err) {
        console.error('Error fetching vendors:', err);
        this.error = 'Failed to fetch approved vendors.';
      } finally {
        this.loading = false;
      }
    },

    formatDate(date) {
      const d = new Date(date);
      return d.toLocaleDateString();
    },

    getBuildingPhoto(photoPath) {
      return `http://localhost:8000/storage/${photoPath}`;
    },
  },
};
</script>

<style scoped>
/* Wrapper */
.vendors-wrapper {
  padding: 30px;
  background: #0f172a; /* dark blue/black */
  min-height: 100vh;
  color: #f9fafb;
  font-family: 'Segoe UI', sans-serif;
}

.vendors-wrapper h2 {
  text-align: center;
  margin-bottom: 40px;
  font-size: 2.2rem;
  font-weight: 700;
  color: #22c55e; /* green accent */
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Loading/Error */
.loading, .error {
  text-align: center;
  font-size: 16px;
  margin-bottom: 20px;
  color: #f87171; /* red for error */
}

/* Grid */
.vendors-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
}

/* Vendor Card */
.vendor-card {
  background: linear-gradient(145deg, #1f2937, #111827);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
}

.vendor-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.8);
}

/* Photo */
.photo-container {
  width: 100%;
  overflow: hidden;
}

.vendor-photo {
  width: 100%;
  height: 180px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.vendor-card:hover .vendor-photo {
  transform: scale(1.05);
}

/* Info */
.vendor-info {
  padding: 20px;
}

.vendor-info h3 {
  color: #22c55e;
  font-size: 1.5rem;
  margin-bottom: 12px;
}

.vendor-info p {
  font-size: 0.95rem;
  line-height: 1.5;
  margin: 6px 0;
}

.vendor-info p span {
  font-weight: 600;
  color: #22c55e;
}

/* Responsive */
@media (max-width: 480px) {
  .vendors-wrapper h2 {
    font-size: 1.8rem;
  }
  .vendor-photo {
    height: 140px;
  }
  .vendor-info h3 {
    font-size: 1.3rem;
  }
  .vendor-info p {
    font-size: 0.9rem;
  }
}
</style>
