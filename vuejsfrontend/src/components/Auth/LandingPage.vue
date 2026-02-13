<template>
  <div class="landing-container">

    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="logo">Ghumau Sauraha</div>
      <ul class="nav-links">  
        <li><a @click.prevent="redirectToLogin">Home</a></li>
        <li><a @click.prevent="redirectToLogin">Services</a></li>
        <li><a @click.prevent="redirectToLogin">Packages</a></li>
        <li><a @click.prevent="redirectToLogin">About Us</a></li>
        <li><a @click.prevent="redirectToLogin">Contact</a></li>
        <li><a @click.prevent="redirectToLogin" class="login-btn">Login</a></li>
        <li><a @click.prevent="goToRegister" class="register-btn">Register</a></li>
        <li class="admin-icon" @click="goToAdmin">
          <i class="fa-solid fa-user"></i>
        </li>
      </ul>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
      <div class="hero-left">
        <h1>Welcome to <span>Ghumau Sauraha</span></h1>
        <p>Your gateway to adventure, culture & unforgettable experiences in the heart of Chitwan.</p>
        <button class="explore-btn" @click="scrollToServices">Explore Now</button>

        <div class="adventure-animate">
          <button class="adventure-btn">
            <strong>Book Your Adventure Here</strong> <span class="arrow">&#8595;</span>
          </button>
        </div>
      </div>

      <div class="hero-right">
        <h2>Register Your Business Here</h2>
        <p>Start offering your services to thousands of travelers visiting Chitwan!</p>
        <div class="vendor-buttons">
          <button @click="goToVendorLogin">Vendor Login</button>
          <button @click="goToVendorRegister">Register Your Business</button>
        </div>
      </div>
    </section>

    <!-- SERVICES SECTION -->
    <section class="services-section" ref="servicesSection">
      <h2 class="section-title">Our Services</h2>
      <div class="services-grid">
        <div v-for="service in services" :key="service.id" class="service-card">
          <img v-if="service.image" :src="`http://localhost:8000/storage/${service.image}`" class="service-img" />
          <h3>{{ service.name }}</h3>
          <p>{{ service.description }}</p>
          <p class="price">Rs. {{ service.price }}</p>
          <button class="book-btn" @click="handleBooking(service.id)">Book Now</button>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
      <div class="footer-content">
        <div class="footer-section">
          <h3>Quick Links</h3>
          <ul>
            <li><a @click.prevent="redirectToLogin">Home</a></li>
            <li><a @click.prevent="redirectToLogin">Services</a></li>
            <li><a @click.prevent="redirectToLogin">Packages</a></li>
            <li><a @click.prevent="redirectToLogin">About Us</a></li>
          </ul>
        </div>

        <div class="footer-section">
          <h3>Contact Us</h3>
          <p>Email: info@ghumausauraha.com</p>
          <p>Phone: +977 9800000000</p>
          <div class="subscribe">
            <input type="email" placeholder="Enter your email" v-model="email" />
            <button @click="submitEmail">Submit</button>
          </div>
        </div>

        <div class="footer-section">
          <h3>Follow Us</h3>
          <div class="social-icons">
            <i class="fa-brands fa-facebook-f"></i>
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-twitter"></i>
          </div>
        </div>
      </div>
      <p class="copyright">© 2025 Ghumau Sauraha. All rights reserved.</p>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../../axios';

const services = ref([]);
const servicesSection = ref(null);
const router = useRouter();
const email = ref('');

const fetchServices = async () => {
  try {
    const res = await axios.get('/admin/services');
    services.value = res.data;
  } catch (err) {
    console.error('Error fetching services:', err);
  }
};

const scrollToServices = () => servicesSection.value.scrollIntoView({ behavior: 'smooth' });

const handleBooking = (serviceId) => {
  const token = localStorage.getItem('auth_token');
  if (!token) router.push({ name: 'Login' });
  else router.push({ name: 'Login', params: { serviceId } });
};

// Vendor routing updated
const goToVendorLogin = () => router.push({ name: 'VendorLogin' });
const goToVendorRegister = () => router.push({ name: 'VendorRegister' });

const goToAdmin = () => router.push({ name: 'AdminLogin' });
const goToRegister = () => router.push({ name: 'Register' });
const redirectToLogin = () => router.push({ name: 'Login' });

const submitEmail = () => {
  if (!email.value) return alert('Please enter an email');
  alert(`Thank you! We will contact you at ${email.value}`);
  email.value = '';
};

onMounted(() => fetchServices());
</script>

<style scoped>
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css");

.landing-container {
  font-family: 'Poppins', sans-serif;
  background-color:#0a0a0a;
  color:white;
  display:flex;
  flex-direction:column;
  min-height:100vh;
}

/* NAVBAR */
.navbar { display:flex; justify-content:space-between; align-items:center; padding:1rem 4rem; background-color:#111827; }
.navbar .logo { font-size:1.5rem; font-weight:bold; color:#22c55e; }
.nav-links { display:flex; gap:1.5rem; list-style:none; align-items:center; }
.nav-links li a { color:white; text-decoration:none; font-weight:500; cursor:pointer; }
.nav-links li a:hover { color:#22c55e; }
.register-btn, .login-btn { padding:0.4rem 0.9rem; border-radius:6px; font-weight:600; color:white; background-color:#22c55e; }
.register-btn:hover, .login-btn:hover { background-color:#16a34a; }
.admin-icon { cursor:pointer; font-size:1.2rem; color:#fff; margin-left:0.5rem; }
.admin-icon:hover { color:#22c55e; }

/* HERO */
.hero {
  display:flex;
  height:70vh;
  background: url('@/assets/tiger.png') center/cover no-repeat;
  padding:4rem;
  align-items:center;
  justify-content:space-between;
  gap:2rem;
}

/* Overlay for readability */
.hero-left, .hero-right {
  background-color: rgba(0,0,0,0.4);
  padding:1rem;
  border-radius:12px;
}

.hero-left h1 span { color:#22c55e; }
.hero-left p { max-width:500px; margin:1rem 0; }
.explore-btn { padding:0.75rem 1.5rem; background-color:#22c55e; border:none; border-radius:8px; font-weight:600; cursor:pointer; margin-top:1rem; }

.adventure-animate { margin-top:2rem; display:flex; justify-content:center; animation:bounce 2s infinite; }
.adventure-btn { background-color:#22c55e; border:none; padding:0.8rem 1.5rem; border-radius:10px; font-size:1.2rem; font-weight:bold; color:white; cursor:pointer; display:flex; align-items:center; gap:0.5rem; }
.adventure-btn .arrow { font-size:1.5rem; }
@keyframes bounce { 0%,100%{transform:translateY(0);}50%{transform:translateY(10px);} }

/* Vendor Panel */
.hero-right { flex:1; text-align:center; }
.vendor-buttons { display:flex; flex-direction:column; gap:1rem; margin-top:1rem; }
.vendor-buttons button { padding:0.75rem 1.25rem; border:none; border-radius:8px; background-color:#22c55e; cursor:pointer; color:white; font-weight:600; }
.vendor-buttons button:hover { background-color:#16a34a; }

/* SERVICES */
.services-section { padding:2rem 2rem 4rem 2rem; margin-top:-3rem; }
.section-title { text-align:center; font-size:2rem; margin-bottom:2rem; color:#22c55e; }
.services-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1.5rem; }
.service-card { background:#1f2937; padding:1rem; border-radius:14px; text-align:center; transition: transform 0.3s; }
.service-card:hover { transform:translateY(-5px); }
.service-img { width:100%; height:180px; object-fit:cover; border-radius:10px; }
.price { margin:0.5rem 0; color:#22c55e; font-weight:600; }
.book-btn { padding:0.5rem 1.25rem; border:none; background:#3b82f6; border-radius:8px; cursor:pointer; transition:0.3s; }
.book-btn:hover { background-color:#2563eb; }

/* FOOTER */
.footer { background-color:#111827; padding:2rem 4rem; margin-top:auto; display:flex; flex-direction:column; gap:2rem; }
.footer-content { display:flex; justify-content:space-between; flex-wrap:wrap; gap:2rem; }
.footer-section h3 { color:#22c55e; margin-bottom:0.5rem; }
.footer-section ul { list-style:none; padding:0; display:flex; flex-direction:column; gap:0.5rem; }
.footer-section ul li a { color:white; text-decoration:none; cursor:pointer; }
.footer-section ul li a:hover { color:#22c55e; }
.footer-content .subscribe { display:flex; gap:0.5rem; }
.footer-content input { padding:0.5rem; border-radius:6px; border:none; width:200px; }
.footer-content button { padding:0.5rem 1rem; border:none; background-color:#22c55e; border-radius:6px; cursor:pointer; font-weight:600; }
.footer-content button:hover { background-color:#16a34a; }
.footer .social-icons i { font-size:1.5rem; margin-right:0.5rem; cursor:pointer; }
.footer p { text-align:center; font-size:0.9rem; color:#9ca3af; }
</style>
