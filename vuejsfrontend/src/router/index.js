import { createRouter, createWebHistory } from 'vue-router'

// User components
import LandingPage from '../components/Auth/LandingPage.vue'
import Login from '../components/Auth/Login.vue'
import Register from '../components/Auth/Register.vue'
import UserProfile from '../components/Auth/UserProfile.vue'
import Home from '../components/Auth/Home.vue'
import ServiceBooking from '../components/Auth/ServiceBooking.vue'
import ApprovedVendorsUser from '../components/Auth/ApprovedVendorsUser.vue'

// Admin components
import AdminLogin from '../components/Admin/AdminLogin.vue'
import AdminDashboard from '../components/Admin/AdminDashboard.vue'
import Services from '../components/Admin/Services.vue'
import ServiceRequest from '../components/Admin/ServiceRequest.vue'

import ServiceList from '../components/Auth/ServiceList.vue'
import VendorRequests from '../components/Admin/VendorRequests.vue';
import ApprovedVendors from '../components/Admin/ApprovedVendors.vue'
import RejectedVendors from '../components/Admin/RejectedVendors.vue'


// Vendor components
// Vendor components
import VendorLogin from '../components/Vendor/VendorLogin.vue';
import VendorRegister from '../components/Vendor/VendorRegister.vue';
// Correct path to your component
import VendorDashboard from '../components/Vendor/VendorDashboard.vue'




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
  { path: '/admin/vendors', name: 'VendorRequests', component: VendorRequests, meta: { requiresAdminAuth: true } },
   { path: '/admin/vendors/accepted', component: ApprovedVendors,meta: { requiresAdminAuth: true }  }, // Approved
  { path: '/admin/vendors/rejected', component: RejectedVendors ,meta: { requiresAdminAuth: true } }, 
  {
    path: '/admin/service-requests',
    component: ServiceRequest
  },
   
  { path: '/services/:type', component: ServiceList },
  { path: '/vendor/login', name: 'VendorLogin', component: VendorLogin },
{ path: '/vendor/register', name: 'VendorRegister', component: VendorRegister },
 {
    path: '/vendor/dashboard',
    name: 'VendorDashboard',
    component: VendorDashboard
  },
  {
  path: '/vendors',
  name: 'ApprovedVendors',
  component: ApprovedVendorsUser,
  meta: { requiresAuth: true }
},

  
  {
 path: '/vendor/service/edit/:id',
  name: 'EditService',
  component: () => import('@/components/Vendor/EditService.vue')
},
  {
    path: '/service/booking/:id',
    name: 'ServiceBooking',  // must match what you use in router.push
    component: ServiceBooking,
    props: true
  },
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
// Global guard

export default router
