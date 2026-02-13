<template>
  <div class="vendor-login-wrapper">
    <div class="vendor-login-card">
      <h2>Vendor Login</h2>

      <form @submit.prevent="login">

        <!-- Email -->
        <div class="form-group">
          <label>Email</label>
          <input type="email" v-model="email" placeholder="Enter email" required />
        </div>

        <!-- Password -->
        <div class="form-group">
          <label>Password</label>
          <input type="password" v-model="password" placeholder="Enter password" required />
        </div>

        <!-- Errors -->
        <div v-if="errors.length" class="error-msg">
          <p v-for="(err, i) in errors" :key="i">{{ err }}</p>
        </div>

        <!-- Success -->
        <p v-if="success" class="success-msg">{{ success }}</p>

        <button type="submit">Login</button>
      </form>

      <p class="link">
        Don't have an account?
        <router-link to="/vendor/register">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const errors = ref([])
const success = ref('')
const router = useRouter()

const login = async () => {
  errors.value = []
  success.value = ''

  // Frontend validation
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if(!emailPattern.test(email.value)) errors.value.push('Invalid email format')
  if(!password.value) errors.value.push('Password is required')
  if(errors.value.length) return

  try {
  // Call backend login API
  const res = await axios.post('http://localhost:8000/api/vendor/login', {
    email: email.value,
    password: password.value
  })

  success.value = res.data.message

  // Save token in localStorage for authenticated requests
  localStorage.setItem('vendor_token', res.data.token)

  // Redirect to vendor dashboard after successful login
  setTimeout(() => {
    router.push('/vendor/dashboard')
  }, 1000)

} catch(err) {
  if (err.response?.status === 403) {
    // Handle not-approved vendor
    errors.value = [err.response.data.message || 'Your account is not approved yet.']
  } else if(err.response?.data?.errors) {
    errors.value = Object.values(err.response.data.errors).flat()
  } else {
    errors.value = [err.response?.data?.message || 'Login failed']
  }
}
}
</script>

<style scoped>
.vendor-login-wrapper {
  min-height: 100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background:#000; 
  font-family: 'Poppins', sans-serif;
}

.vendor-login-card {
  background:#111827;
  padding: 40px 35px;
  width: 400px;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
  color:white;
}

h2 {
  text-align:center;
  margin-bottom:25px;
  color:#22c55e;
}

.form-group {
  margin-bottom:15px;
  display:flex;
  flex-direction:column;
}

.form-group label { margin-bottom:5px; font-weight:600; }

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
  padding:10px;
  border-radius:8px;
  border:none;
  font-size:14px;
  background:#1f2937;
  color:white;
}

button {
  width:100%;
  padding:12px;
  margin-top:10px;
  background:#22c55e;
  border:none;
  border-radius:8px;
  font-weight:600;
  cursor:pointer;
}

button:hover { background:#16a34a; }

.error-msg { color:#f87171; margin-top:10px; font-weight:600; }
.success-msg { color:#22c55e; margin-top:10px; font-weight:600; }

.link { text-align:center; margin-top:15px; }
.link a { color:#22c55e; font-weight:600; text-decoration:none; }
.link a:hover { color:#16a34a; }

@media(max-width:550px){
  .vendor-login-card{ width:90%; padding:30px 20px; }
}
</style>
