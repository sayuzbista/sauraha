<template>
  <div class="vendor-register-wrapper">
    <div class="vendor-register-card">
      <h2>Vendor Registration</h2>

      <form @submit.prevent="register" autocomplete="off">

        <!-- Company Name -->
        <div class="form-group">
          <label>Company Name</label>
          <input type="text" v-model="company_name" placeholder="Enter company name" required autocomplete="off"/>
        </div>

        <!-- Owner / Manager Name -->
        <div class="form-group">
          <label>Owner / Manager Name</label>
          <input type="text" v-model="owner_name" placeholder="Enter owner name" required autocomplete="off"/>
        </div>

        <!-- Location -->
        <div class="form-group">
          <label>Location</label>
          <input type="text" v-model="location" placeholder="Enter company location" required autocomplete="off"/>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label>Email</label>
          <input type="email" v-model="email" placeholder="Enter email" required autocomplete="off"/>
        </div>

        <!-- Phone Number -->
        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" v-model="phone" placeholder="Enter phone number" required autocomplete="off"/>
        </div>

        <!-- PAN Number -->
        <div class="form-group">
          <label>PAN Number</label>
          <input type="text" v-model="pan_number" placeholder="Enter PAN number" required autocomplete="off"/>
        </div>

        <!-- Services Provided -->
        <div class="form-group">
          <label>Services Provided</label>
          <div class="checkbox-group">
            <label><input type="checkbox" value="Canoeing" v-model="services"/> Canoeing</label>
            <label><input type="checkbox" value="Quadbike" v-model="services"/> Quadbike</label>
            <label><input type="checkbox" value="Elephant Safari" v-model="services"/> Elephant Safari</label>
            <label><input type="checkbox" value="Jeep Safari" v-model="services"/> Jeep Safari</label>
            <label><input type="checkbox" value="Other" v-model="services"/> Other</label>
          </div>
          <input v-if="services.includes('Other')" type="text" v-model="other_service" placeholder="Specify other service" autocomplete="off"/>
        </div>

        <!-- Opening and Closing Time -->
        <div class="form-group">
          <label>Opening Time</label>
          <input type="time" v-model="opening_time" required />
        </div>
        <div class="form-group">
          <label>Closing Time</label>
          <input type="time" v-model="closing_time" required />
        </div>

        <!-- Company Doc -->
        <div class="form-group">
          <label>Company Registration Document</label>
          <input type="file" @change="handleCompanyDoc" required />
        </div>

        <!-- Building Photo -->
        <div class="form-group">
          <label>Company Building Photo</label>
          <input type="file" @change="handleBuildingPhoto" required />
        </div>

        <!-- Password -->
        <div class="form-group">
          <label>Password</label>
          <input type="password" v-model="password" placeholder="Enter password" required autocomplete="new-password"/>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" v-model="confirm_password" placeholder="Confirm password" required autocomplete="new-password"/>
        </div>

        <!-- Backend Error Messages -->
        <div v-if="backendErrors.length" class="error-msg">
          <p v-for="(err, index) in backendErrors" :key="index">{{ err }}</p>
        </div>

        <!-- Success Message -->
        <p v-if="success" class="success-msg">{{ success }}</p>

        <button type="submit">Register</button>

      </form>

      <p class="link">
        Already have an account?
        <router-link to="/vendor/login">Login</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const company_name = ref('')
const owner_name = ref('')
const location = ref('')
const email = ref('')
const phone = ref('')
const pan_number = ref('')
const services = ref([])
const other_service = ref('')
const opening_time = ref('')
const closing_time = ref('')
const password = ref('')
const confirm_password = ref('')
const company_doc = ref(null)
const building_photo = ref(null)
const backendErrors = ref([])
const success = ref('')

const router = useRouter()

const handleCompanyDoc = e => company_doc.value = e.target.files[0]
const handleBuildingPhoto = e => building_photo.value = e.target.files[0]

// Frontend Validation
const validateForm = () => {
  backendErrors.value = []

  // Email validation
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if(!emailPattern.test(email.value)) { backendErrors.value.push('Invalid email format') }

  // Phone validation
  const phonePattern = /^\d{10,15}$/
  if(!phonePattern.test(phone.value)) { backendErrors.value.push('Invalid phone number') }

  // PAN validation
  const panPattern = /^[A-Za-z0-9]{5,15}$/
  if(!panPattern.test(pan_number.value)) { backendErrors.value.push('Invalid PAN number') }

  // Password validation
  const pwdPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/
  if(!pwdPattern.test(password.value)) { backendErrors.value.push('Password must be at least 8 chars including uppercase, lowercase, number, special') }

  if(password.value !== confirm_password.value) { backendErrors.value.push('Passwords do not match') }

  return backendErrors.value.length === 0
}

// Submit
const register = async () => {
  if(!validateForm()) return

  try {
    const formData = new FormData()
    formData.append('company_name', company_name.value)
    formData.append('owner_name', owner_name.value)
    formData.append('location', location.value)
    formData.append('email', email.value)
    formData.append('phone_number', phone.value)
    formData.append('pan_number', pan_number.value)

    // Append services array correctly
    const finalServices = [...services.value.filter(s => s!=='Other')]
    if(services.value.includes('Other') && other_service.value) finalServices.push(other_service.value)
    finalServices.forEach(s => formData.append('services_provided[]', s))

    formData.append('opening_time', opening_time.value)
    formData.append('closing_time', closing_time.value)
    formData.append('password', password.value)
    formData.append('password_confirmation', confirm_password.value)
    formData.append('company_doc', company_doc.value)
    formData.append('building_photo', building_photo.value)

    const res = await axios.post('http://localhost:8000/api/vendor/register', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    success.value = res.data.message
    backendErrors.value = []
    setTimeout(()=>router.push('/'), 3000)
  } catch (err) {
    console.error(err)
    if(err.response?.data?.errors){
      backendErrors.value = Object.values(err.response.data.errors).flat()
    } else {
      backendErrors.value = [err.response?.data?.message || 'Registration failed']
    }
    success.value = ''
  }
}
</script>

<style scoped>
.vendor-register-wrapper {
  min-height: 100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background:#000; 
  font-family: 'Poppins', sans-serif;
}

.vendor-register-card {
  background:#111827;
  padding: 40px 35px;
  width: 500px;
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
.form-group input[type="password"],
.form-group input[type="time"],
.form-group input[type="file"] {
  padding:10px;
  border-radius:8px;
  border:none;
  font-size:14px;
  background:#1f2937;
  color:white;
}

.checkbox-group {
  display:flex;
  flex-wrap:wrap;
  gap:10px;
}

.checkbox-group label {
  display:flex;
  align-items:center;
  gap:5px;
  font-weight:500;
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
  .vendor-register-card{ width:90%; padding:30px 20px; }
}
</style>
