import { createRouter, createWebHistory } from 'vue-router'
import ProductsView from '@/components/ProductsView.vue'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import PlaceholderView from '@/views/PlaceholderView.vue'
import TrackedProductsView from '@/views/TrackedProductsView.vue'
import AdminView from '@/views/AdminView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    redirect: { path: '/products' }
  },
  {
    path: '',
    redirect: { path: '/products' }
  },
  {
    path: '/products',
    name: 'products',
    component: ProductsView
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView
  },
  {
    path: '/profile',
    name: 'profile',
    component: PlaceholderView
  },
  {
    path: '/tracked-products',
    name: 'tracked-products',
    component: TrackedProductsView
  },
  {
    path: '/admin',
    name: 'admin',
    component: AdminView
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    redirect: { path: '/products' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
