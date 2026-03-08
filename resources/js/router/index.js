import { createRouter, createWebHistory } from 'vue-router'
import ProductsView from '@/components/ProductsView.vue'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import ProfileView from '@/views/ProfileView.vue'
import TrackedProductsView from '@/views/TrackedProductsView.vue'
import AdminView from '@/views/AdminView.vue'
import CatalogView from '@/views/CatalogView.vue'
import GroupCategoriesView from '@/views/GroupCategoriesView.vue'
import AdminCategoriesView from '@/views/AdminCategoriesView.vue'

const routes = [
  {
    path: '/',
    redirect: { path: '/kategorijas' }
  },
  {
    path: '/kategorijas',
    name: 'categories',
    component: CatalogView
  },
  {
    path: '/:groupSlug/kategorijas',
    name: 'group-categories',
    component: GroupCategoriesView
  },
  {
    path: '/products',
    name: 'products',
    component: ProductsView
  },
  {
    path: '/:groupSlug/products',
    name: 'group-products',
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
    component: ProfileView
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
    path: '/admin/categories',
    name: 'admin-categories',
    component: AdminCategoriesView
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    redirect: { path: '/kategorijas' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
