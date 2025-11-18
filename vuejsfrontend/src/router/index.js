import { createRouter, createWebHistory } from 'vue-router'

// User components
import LandingPage from '../components/Auth/LandingPage.vue'
import Login from '../components/Auth/Login.vue'
import Register from '../components/Auth/Register.vue'
import UserProfile from '../components/Auth/UserProfile.vue'
import Home from '../components/Auth/Home.vue'

// Admin components
import AdminLogin from '../components/Admin/AdminLogin.vue'
import AdminDashboard from '../components/Admin/AdminDashboard.vue'
import Services from '../components/Admin/Services.vue'

const routes = [
  // Landing page
  { path: '/', name: 'Landing', component: LandingPage },

  // Auth
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/home', name: 'Home', component: Home, meta: { requiresAuth: true } },
  { path: '/userprofile', name: 'UserProfile', component: UserProfile, meta: { requiresAuth: true } },

  // Admin
  { path: '/admin/login', name: 'AdminLogin', component: AdminLogin },
  { path: '/admin/dashboard', name: 'AdminDashboard', component: AdminDashboard, meta: { requiresAdminAuth: true } },
  { path: '/admin/services', name: 'Services', component: Services, meta: { requiresAdminAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard for authentication
router.beforeEach((to, from, next) => {
  const userToken = localStorage.getItem('auth_token')
  const adminToken = localStorage.getItem('admin_token') // optional if you have admin auth

  // Protect user routes
  if (to.meta.requiresAuth && !userToken) {
    next({ name: 'Login' })
  }
  // Protect admin routes
  else if (to.meta.requiresAdminAuth && !adminToken) {
    next({ name: 'AdminLogin' })
  } else {
    next()
  }
})

export default router
