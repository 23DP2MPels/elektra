<template>
  <v-app-bar elevation="2" color="white">
    <v-container fluid class="d-flex align-center">
      <v-app-bar-title>
        <router-link to="/kategorijas" class="logo syne-font text-decoration-none">
          ELEKTRA
        </router-link>
      </v-app-bar-title>

      <v-spacer></v-spacer>

      <v-text-field
        v-model="searchQuery"
        prepend-inner-icon="mdi-magnify"
        label="Meklēt elektropreces..."
        variant="solo"
        density="compact"
        hide-details
        rounded
        class="search-field mx-4"
        style="max-width: 500px"
        @update:model-value="handleSearch"
      ></v-text-field>

      <v-spacer></v-spacer>

      <div class="d-flex ga-2 align-center">
        <v-btn
          v-if="comparisonCount > 0"
          variant="flat"
          color="secondary"
          @click="openComparisonPanel"
        >
          <v-icon start>mdi-compare</v-icon>
          Salīdzināt izvēlēto ({{ comparisonCount }})
        </v-btn>
        <template v-if="!authStore.isAuthenticated">
          <v-btn variant="text" color="primary" @click="$router.push('/login')">
            Pieslēgties
          </v-btn>
          <v-btn variant="flat" color="primary" @click="$router.push('/register')">
            Reģistrēties
          </v-btn>
        </template>

        <template v-else>
          <v-btn icon="mdi-heart-outline" variant="text" @click="$router.push('/tracked-products')"></v-btn>
          <v-menu>
            <template v-slot:activator="{ props }">
              <v-btn icon="mdi-account-circle" variant="text" v-bind="props"></v-btn>
            </template>
            <v-list>
              <v-list-item>
                <v-list-item-title class="font-weight-bold">{{ authStore.user?.name }}</v-list-item-title>
                <v-list-item-subtitle>{{ authStore.user?.email }}</v-list-item-subtitle>
              </v-list-item>
              <v-divider></v-divider>
              <v-list-item @click="$router.push('/profile')">
                <template v-slot:prepend><v-icon icon="mdi-account"></v-icon></template>
                <v-list-item-title>Profils</v-list-item-title>
              </v-list-item>
              <v-list-item @click="$router.push('/tracked-products')">
                <template v-slot:prepend><v-icon icon="mdi-heart"></v-icon></template>
                <v-list-item-title>Sekotās preces</v-list-item-title>
              </v-list-item>
              <v-list-item v-if="authStore.isAdmin" @click="$router.push('/admin')">
                <template v-slot:prepend><v-icon icon="mdi-shield-account"></v-icon></template>
                <v-list-item-title>Administrācija</v-list-item-title>
              </v-list-item>
              <v-list-item v-if="authStore.isAdmin" @click="$router.push('/admin/categories')">
                <template v-slot:prepend><v-icon icon="mdi-tag-multiple"></v-icon></template>
                <v-list-item-title>Kategorijas</v-list-item-title>
              </v-list-item>
              <v-divider></v-divider>
              <v-list-item @click="handleLogout">
                <template v-slot:prepend><v-icon icon="mdi-logout"></v-icon></template>
                <v-list-item-title>Iziet</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </div>
    </v-container>
  </v-app-bar>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useProductStore } from '@/stores/products'
import { useSnackbarStore } from '@/stores/snackbar'

const router = useRouter()
const authStore = useAuthStore()
const productStore = useProductStore()
const snackbarStore = useSnackbarStore()

const comparisonCount = inject('comparisonCount')
const openComparisonPanel = inject('openComparisonPanel', () => {})

const searchQuery = ref('')

const handleSearch = () => {
  productStore.setFilter('search', searchQuery.value)
  if (router.currentRoute.value.name !== 'products') {
    router.push('/products')
  }
}

const handleLogout = async () => {
  await authStore.logout()
  snackbarStore.showSuccess('Jūs esat veiksmīgi izgājis')
  router.push('/kategorijas')
}
</script>

<style scoped>
.logo {
  font-size: 1.75rem;
  font-weight: 800;
  background: linear-gradient(135deg, #FF6B35, #004E89);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: -1px;
}
.search-field { flex: 1; }
</style>
