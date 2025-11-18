<template>
  <footer class="footer">
    <div class="footer-top">
      <div class="footer-section">
        <h3>About Ghumau Sauraha</h3>
        <p>Experience the best adventures, culture, and hospitality in Chitwan. We make your trips unforgettable!</p>
      </div>

      <div class="footer-section">
        <h3>Quick Links</h3>
        <ul>
          <li><router-link to="/">Home</router-link></li>
          <li><router-link to="/login">Login</router-link></li>
          <li><router-link to="/register">Register</router-link></li>
          <li><router-link to="/contact">Contact</router-link></li>
        </ul>
      </div>

      <div class="footer-section">
        <h3>Contact Us</h3>
        <form @submit.prevent="submitEmail">
          <input type="email" v-model="email" placeholder="Enter your email" required />
          <button type="submit">Submit</button>
        </form>
        <p v-if="successMessage" class="success">{{ successMessage }}</p>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; 2025 Ghumau Sauraha. All rights reserved.</p>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue';

const email = ref('');
const successMessage = ref('');

const submitEmail = () => {
  // For now, just show a success message
  successMessage.value = `Thank you! We will contact you at ${email.value}.`;
  email.value = '';
};
</script>

<style scoped>
.footer {
  background-color: #111827;
  color: white;
  font-family: 'Poppins', sans-serif;
  padding: 3rem;
}

.footer-top {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  margin-bottom: 2rem;
}

.footer-section {
  flex: 1 1 250px;
  margin: 1rem;
}

.footer-section h3 {
  color: #22c55e;
  margin-bottom: 1rem;
}

.footer-section p,
.footer-section li,
.footer-section a {
  color: #d1d5db;
  font-size: 0.95rem;
}

.footer-section a:hover {
  color: #22c55e;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section form {
  display: flex;
  flex-direction: column;
}

.footer-section input {
  padding: 0.5rem;
  margin-bottom: 0.5rem;
  border-radius: 6px;
  border: none;
}

.footer-section button {
  padding: 0.5rem;
  border: none;
  border-radius: 6px;
  background-color: #22c55e;
  color: white;
  cursor: pointer;
}

.footer-section button:hover {
  background-color: #16a34a;
}

.success {
  color: #3b82f6;
  margin-top: 0.5rem;
}

.footer-bottom {
  text-align: center;
  border-top: 1px solid #374151;
  padding-top: 1rem;
  font-size: 0.9rem;
  color: #9ca3af;
}
</style>
