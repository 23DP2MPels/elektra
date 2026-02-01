<template>
  <v-container class="py-6">
    <h1 class="text-h4 mb-6">Sekotās preces</h1>

    <v-progress-linear v-if="loading" indeterminate color="primary" class="mb-4"></v-progress-linear>

    <template v-else-if="!authStore.isAuthenticated">
      <v-alert type="info" variant="tonal" class="mb-4">
        Lai redzētu sekotās preces, lūdzu, <router-link to="/login" class="font-weight-bold">pieslēdzieties</router-link>.
      </v-alert>
    </template>

    <template v-else-if="products.length === 0">
      <v-alert type="info" variant="tonal">
        Jūs vēl nesekojat nevienai precei. Izvēlieties preci katalogā un noklikšķiniet "Sekot cenai".
      </v-alert>
    </template>

    <v-row v-else>
      <v-col v-for="product in products" :key="product.preces_id ?? product.id" cols="12" sm="6" md="4">
        <v-card class="product-card" elevation="2" rounded="lg">
          <v-img
            :src="product.attels_url || product.image_url || placeholderImage"
            height="180"
            contain
            class="bg-grey-lighten-4"
          ></v-img>
          <v-card-text>
            <v-chip size="small" color="secondary" variant="tonal" class="mb-2">
              {{ product.category?.nosaukums ?? product.category?.name }}
            </v-chip>
            <h3 class="text-h6 mb-2">{{ product.nosaukums ?? product.name }}</h3>
            <div class="d-flex align-center gap-2 mb-2">
              <span class="text-h6 text-primary">€{{ formatPrice(product.price ?? product.current_price) }}</span>
              <span v-if="product.store" class="text-caption text-medium-emphasis">
                <v-icon size="small">mdi-store</v-icon> {{ product.store.nosaukums ?? product.store.name }}
              </span>
            </div>
            <v-btn
              variant="outlined"
              color="error"
              size="small"
              block
              :loading="removingId === (product.preces_id ?? product.id)"
              @click="removeTracking(product)"
            >
              <v-icon start>mdi-heart-off</v-icon>
              Noņemt no sekotajām
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useSnackbarStore } from '@/stores/snackbar'
import api from '@/services/api'
import { formatPrice } from '@/utils/price'

const authStore = useAuthStore()
const snackbarStore = useSnackbarStore()

const loading = ref(true)
const products = ref([])
const removingId = ref(null)

const placeholderImage = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f0f0f0" width="200" height="200"/%3E%3C/svg%3E'

async function fetchTrackedProducts() {
  if (!authStore.isAuthenticated) {
    loading.value = false
    return
  }
  loading.value = true
  try {
    const res = await api.get('/tracked-products')
    if (res.data?.success && Array.isArray(res.data.data)) {
      products.value = res.data.data
    } else {
      products.value = []
    }
  } catch (err) {
    if (err.response?.status === 401) {
      products.value = []
    } else {
      snackbarStore.showError('Neizdevās ielādēt sekotās preces')
      products.value = []
    }
  } finally {
    loading.value = false
  }
}

async function removeTracking(product) {
  const id = product.preces_id ?? product.id
  removingId.value = id
  try {
    await api.delete(`/tracked-products/${id}`)
    products.value = products.value.filter(p => (p.preces_id ?? p.id) !== id)
    snackbarStore.showSuccess('Prece noņemta no sekotajām')
  } catch (err) {
    snackbarStore.showError(err.response?.data?.message || 'Neizdevās noņemt')
  } finally {
    removingId.value = null
  }
}

onMounted(() => {
  fetchTrackedProducts()
})
</script>

<style scoped>
.product-card { transition: transform 0.2s; }
.product-card:hover { transform: translateY(-4px); }
</style>
