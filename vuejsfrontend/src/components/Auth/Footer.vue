<template>
  <footer class="footer">
    <div class="footer-content">
      <!-- About -->
      <div class="footer-section about">
        <h3>Ghumau Sauraha</h3>
        <p>
          Explore the heart of Chitwan with our curated adventures. Culture, wildlife, and unforgettable experiences await.
        </p>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="footer-section links">
        <h4>Quick Links</h4>
        <ul>
          <li><a @click.prevent="$router.push({ name: 'Home' })">Home</a></li>
          <li><a @click.prevent="$router.push({ name: 'Packages' })">Packages</a></li>
          <li><a @click.prevent="$router.push({ name: 'ServiceList' })">Services</a></li>
          <li><a @click.prevent="$router.push({ name: 'Contact' })">Contact</a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div class="footer-section contact">
        <h4>Contact Us</h4>
        <p>Email: info@ghumausauraha.com</p>
        <p>Phone: +977-9800000000</p>
        <p>Address: Sauraha, Chitwan, Nepal</p>
      </div>

      <!-- Newsletter -->
      <div class="footer-section newsletter">
        <h4>Subscribe</h4>
        <p>Get updates on new adventures and packages</p>
        <div class="subscribe">
          <input type="email" placeholder="Enter your email" v-model="email" />
          <button @click="submitEmail">Subscribe</button>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 Ghumau Sauraha. All Rights Reserved.</p>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('')

const submitEmail = () => {
  if (!email.value) return alert('Please enter a valid email')
  alert(`Thank you! Updates will be sent to ${email.value}`)
  email.value = ''
}
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');

.footer {
  background: #111827;
  color: #ffffff;
  padding: 4rem 2rem 2rem 2rem;
  font-family: 'Poppins', sans-serif;
}

.footer-content {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 2rem;
}

.footer-section {
  flex: 1 1 200px;
  min-width: 200px;
}

.footer-section h3,
.footer-section h4 {
  color: #22c55e;
  margin-bottom: 1rem;
}

.footer-section p,
.footer-section li {
  color: #d1d5db;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li a {
  color: #d1d5db;
  text-decoration: none;
  cursor: pointer;
  transition: 0.3s;
}

.footer-section ul li a:hover {
  color: #22c55e;
}

.social-icons a {
  display: inline-block;
  background: #22c55e;
  color: white;
  width: 36px;
  height: 36px;
  line-height: 36px;
  text-align: center;
  border-radius: 50%;
  margin-right: 0.5rem;
  transition: 0.3s;
}

.social-icons a:hover {
  background: #16a34a;
  transform: scale(1.1);
}

.subscribe {
  display: flex;
  margin-top: 0.5rem;
}

.subscribe input {
  flex: 1;
  padding: 0.5rem 0.75rem;
  border-radius: 6px 0 0 6px;
  border: none;
  outline: none;
}

.subscribe button {
  padding: 0.5rem 1rem;
  border: none;
  background: #22c55e;
  color: white;
  border-radius: 0 6px 6px 0;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}

.subscribe button:hover {
  background: #16a34a;
}

.footer-bottom {
  text-align: center;
  margin-top: 2rem;
  border-top: 1px solid #374151;
  padding-top: 1rem;
  color: #9ca3af;
  font-size: 0.85rem;
}
</style>
