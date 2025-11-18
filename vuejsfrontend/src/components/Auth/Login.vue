<template>
    <div class="container">
      <div class="card">
        <h2 class="title">{{ isLogin ? 'Login' : 'Register' }}</h2>
  
        <form @submit.prevent="isLogin ? login() : register()" enctype="multipart/form-data">
          <template v-if="!isLogin">
            <input
              v-model="name"
              type="text"
              placeholder="Name"
              class="input"
              required
            />
          </template>
  
          <input
            v-model="email"
            type="email"
            placeholder="Email"
            class="input"
            required
          />
          <input
            v-model="password"
            type="password"
            placeholder="Password"
            class="input"
            required
          />
  
          <template v-if="!isLogin">
            <input
              v-model="password_confirmation"
              type="password"
              placeholder="Confirm Password"
              class="input"
              required
            />
            <input
              v-model="phone_number"
              type="text"
              placeholder="Phone Number"
              class="input"
            />
            <input type="file" @change="handleFileUpload" class="file-input" />
          </template>
  
          <button type="submit" class="btn">{{ isLogin ? 'Login' : 'Register' }}</button>
  
          <p v-if="error" class="error-msg">{{ error }}</p>
          <div v-if="successMessage" class="popup-success">{{ successMessage }}</div>
        </form>
  
        <p class="toggle-text">
          {{ isLogin ? "Don't have an account?" : 'Already have an account?' }}
          <a href="#" @click.prevent="toggleForm" class="toggle-link">
            {{ isLogin ? 'Register' : 'Login' }}
          </a>
        </p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import axios from 'axios'
  import { useRouter } from 'vue-router'
  
  const router = useRouter()
  
  const isLogin = ref(true)
  
  const name = ref('')
  const email = ref('')
  const password = ref('')
  const password_confirmation = ref('')
  const phone_number = ref('')
  const profile_picture = ref(null)
  
  const error = ref('')
  const successMessage = ref('')
  
  const toggleForm = () => {
    isLogin.value = !isLogin.value
    clearMessages()
    clearFields()
  }
  
  const clearMessages = () => {
    error.value = ''
    successMessage.value = ''
  }
  
  const clearFields = () => {
    name.value = ''
    email.value = ''
    password.value = ''
    password_confirmation.value = ''
    phone_number.value = ''
    profile_picture.value = null
  }
  
  const handleFileUpload = (event) => {
    profile_picture.value = event.target.files[0]
  }
  
  const login = async () => {
    clearMessages()
    try {
      const response = await axios.post('http://localhost:8000/api/login', {
        email: email.value,
        password: password.value,
      })
  
      localStorage.setItem('token', response.data.token)
      successMessage.value = 'Login successful! Redirecting...'
  
      setTimeout(() => {
        router.push('/home') // adjust as needed
      }, 1500)
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
    }
  }
  
  const register = async () => {
    clearMessages()
    try {
      const formData = new FormData()
      formData.append('name', name.value)
      formData.append('email', email.value)
      formData.append('password', password.value)
      formData.append('password_confirmation', password_confirmation.value)
      formData.append('phone_number', phone_number.value)
      if (profile_picture.value) {
        formData.append('profile_picture', profile_picture.value)
      }
  
      const response = await axios.post('http://localhost:8000/api/register', formData)
  
      successMessage.value = 'Registration successful! Redirecting to login...'
      clearFields()
  
      setTimeout(() => {
        isLogin.value = true
        successMessage.value = ''
      }, 2000)
    } catch (err) {
      error.value =
        err.response?.data?.errors?.email?.[0] ||
        err.response?.data?.message ||
        'Registration failed'
    }
  }
  </script>
  
  <style scoped>
  .container {
    min-height: 100vh;
    background-color: #000;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1rem;
  }
  
  .card {
    background-color: #1f2937; /* Tailwind gray-800 */
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 10px 15px -3px rgba(0, 122, 255, 0.5);
    width: 100%;
    max-width: 400px;
    text-align: center;
  }
  
  .title {
    color: #3b82f6; /* Tailwind blue-500 */
    font-size: 2rem;
    margin-bottom: 1.5rem;
    font-weight: 700;
  }
  
  .input {
    width: 100%;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    border: none;
    background-color: #374151; /* Tailwind gray-700 */
    color: #fff;
    font-size: 1rem;
  }
  
  .input::placeholder {
    color: #9ca3af; /* Tailwind gray-400 */
  }
  
  .file-input {
    width: 100%;
    margin-bottom: 1rem;
    color: #9ca3af;
  }
  
  .btn {
    width: 100%;
    padding: 0.75rem;
    background-color: #2563eb; /* Tailwind blue-600 */
    border-radius: 8px;
    border: none;
    color: white;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 1.1rem;
  }
  
  .btn:hover {
    background-color: #1e40af; /* Tailwind blue-700 */
  }
  
  .error-msg {
    margin-top: 1rem;
    color: #ef4444; /* Tailwind red-500 */
    font-weight: 600;
  }
  
  .popup-success {
    margin-top: 1rem;
    padding: 0.75rem;
    background-color: #3b82f6; /* blue-500 */
    color: black;
    font-weight: 700;
    border-radius: 8px;
    animation: fadeinout 3s ease forwards;
  }
  
  @keyframes fadeinout {
    0% {
      opacity: 0;
    }
    10% {
      opacity: 1;
    }
    90% {
      opacity: 1;
    }
    100% {
      opacity: 0;
    }
  }
  
  .toggle-text {
    margin-top: 1.5rem;
    color: #9ca3af;
    font-size: 0.95rem;
  }
  
  .toggle-link {
    color: #3b82f6;
    cursor: pointer;
    font-weight: 600;
    margin-left: 0.3rem;
    text-decoration: underline;
  }
  </style>
  