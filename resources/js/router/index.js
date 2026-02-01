import { createRouter, createWebHistory } from 'vue-router'
import ProductsView from '@/components/ProductsView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    redirect: '/products'
  },
  {
    path: '/products',
    name: 'products',
    component: ProductsView
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
