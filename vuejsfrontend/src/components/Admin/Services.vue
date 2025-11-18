<template>
  <div>
    <Navbar />
    <div class="container">
      <img :src="adminAnimation" class="admin-gif" />

      <h2 class="title">Manage Services</h2>

      <!-- Add/Edit Form -->
      <form @submit.prevent="editing ? updateService() : addService()" class="form">
        <input v-model="name" placeholder="Service Name" required />
        <input v-model="description" placeholder="Description" />
        <input v-model="price" type="number" placeholder="Price" />
        <input type="file" @change="handleFile" />
        <button type="submit">{{ editing ? 'Update' : 'Add' }} Service</button>
        <p v-if="error" class="error-msg">{{ error }}</p>
        <p v-if="success" class="success-msg">{{ success }}</p>
      </form>

      <!-- List of Services -->
      <div class="services-list">
        <div v-for="service in services" :key="service.id" class="service-card">
          <img v-if="service.image" :src="`http://localhost:8000/storage/${service.image}`" />
          <h3>{{ service.name }}</h3>
          <p>{{ service.description }}</p>
          <p>Price: {{ service.price }}</p>
          <div class="actions">
            <button @click="editService(service)">Edit</button>
            <button @click="deleteService(service.id)">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import adminAnimation from "@/assets/admin-animation.gif";

import axios from '../../axios';
import Navbar from './Navbar.vue';

const name = ref('');
const description = ref('');
const price = ref('');
const imageFile = ref(null);
const editing = ref(false);
const editId = ref(null);

const services = ref([]);
const error = ref('');
const success = ref('');

const handleFile = (e) => {
  imageFile.value = e.target.files[0];
};

const fetchServices = async () => {
  try {
    const res = await axios.get('/admin/services');
    services.value = res.data;
  } catch (err) {
    console.error(err);
  }
};

const addService = async () => {
  error.value = '';
  success.value = '';
  try {
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('description', description.value);
    formData.append('price', price.value);
    if (imageFile.value) formData.append('image', imageFile.value);

    await axios.post('/admin/services', formData);
    success.value = 'Service added successfully';
    clearForm();
    fetchServices();
  } catch (err) {
    error.value = err.response?.data?.errors?.name?.[0] || 'Failed to add service';
  }
};

const editService = (service) => {
  editing.value = true;
  editId.value = service.id;
  name.value = service.name;
  description.value = service.description;
  price.value = service.price;
};

const updateService = async () => {
  error.value = '';
  success.value = '';
  try {
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('description', description.value);
    formData.append('price', price.value);
    if (imageFile.value) formData.append('image', imageFile.value);

    await axios.post(`/admin/services/${editId.value}`, formData, { headers: { 'X-HTTP-Method-Override': 'PUT' }});
    success.value = 'Service updated successfully';
    clearForm();
    fetchServices();
    editing.value = false;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update service';
  }
};

const deleteService = async (id) => {
  try {
    await axios.delete(`/admin/services/${id}`);
    fetchServices();
  } catch (err) {
    console.error(err);
  }
};

const clearForm = () => {
  name.value = '';
  description.value = '';
  price.value = '';
  imageFile.value = null;
};

onMounted(() => {
  fetchServices();
});
</script>

<style scoped>
.container {
  padding: 2rem;
  background-color: #000;
  min-height: 90vh;
  color: white;
}

.title {
  color: #3b82f6;
  margin-bottom: 1.5rem;
  font-size: 2rem;
}

.form input {
  display: block;
  width: 100%;
  margin-bottom: 0.75rem;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  border: none;
  background-color: #374151;
  color: white;
  box-sizing: border-box;
}

.form button {
  background-color: #2563eb;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: 600;
  cursor: pointer;
  margin-right: 0.5rem;
}

.form button:hover {
  background-color: #1e40af;
}

.services-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-top: 2rem;
}

.service-card {
  background-color: #1f2937;
  padding: 1rem;
  border-radius: 12px;
  width: calc(33% - 1rem);
  box-sizing: border-box;
  text-align: center;
}

.service-card img {
  max-width: 100%;
  border-radius: 8px;
  margin-bottom: 0.5rem;
}

.actions {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.service-card button {
  margin: 0.25rem;
  padding: 0.4rem 0.75rem;
  font-size: 0.9rem;
  cursor: pointer;
}
.error-msg {
  color: #ef4444;
}
.success-msg {
  color: #3b82f6;
}
</style>
