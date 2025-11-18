<template>
  <div class="container">
    <div class="card">
      <h2 class="title">Admin Login</h2>
      <form @submit.prevent="login">
        <input v-model="username" type="text" placeholder="Username" required />
        <input v-model="password" type="password" placeholder="Password" required />
        <button type="submit" class="btn">Login</button>
        <p v-if="error" class="error-msg">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const username = ref('')
const password = ref('')
const error = ref('')

const login = async () => {
  error.value = ''
  try {
    const res = await axios.post('http://localhost:8000/api/admin/login', {
      username: username.value,
      password: password.value,
    })
    localStorage.setItem('admin_token', res.data.token)
router.push('/admin/dashboard')

  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed'
  }
}
</script>

<style scoped>
/* Full screen container */
.container {
  min-height: 100vh;
  background-color: #000;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem; /* Added padding on all sides */
  box-sizing: border-box;
}

/* Card with max width & spacing */
.card {
  background-color: #1f2937;
  padding: 2.5rem 3rem; /* More internal spacing */
  border-radius: 12px;
  width: 100%;
  max-width: 450px; /* Reasonable width */
  text-align: center;
  color: #fff;
  box-shadow: 0 10px 20px rgba(0, 122, 255, 0.4);
}

/* Title styling */
.title {
  color: #3b82f6;
  font-size: 2rem;
  margin-bottom: 2rem;
  font-weight: 700;
}

/* Input fields */
input {
  width: 100%;
  padding: 0.75rem 1rem;
  margin-bottom: 1rem;
  border-radius: 8px;
  border: none;
  background-color: #374151;
  color: #fff;
  font-size: 1rem;
  box-sizing: border-box;
}

input::placeholder {
  color: #9ca3af;
}

/* Button */
.btn {
  width: 100%;
  padding: 0.75rem;
  background-color: #2563eb;
  border-radius: 8px;
  border: none;
  color: white;
  font-weight: 700;
  cursor: pointer;
  font-size: 1.1rem;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: #1e40af;
}

/* Error message */
.error-msg {
  color: #ef4444;
  margin-top: 1rem;
  font-weight: 600;
}
</style>
