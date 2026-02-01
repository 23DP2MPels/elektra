<template>
  <v-container fluid class="products-view pa-8">
    <div class="text-center mb-12 hero-section">
      <h1 class="syne-font text-h2 font-weight-bold mb-4">Salīdzini un Ietaupi</h1>
      <p class="text-h6 text-medium-emphasis">
        Viedā platforma elektropreču cenu un specifikāciju salīdzināšanai Latvijā
      </p>
    </div>

    <v-card elevation="2" rounded="lg" class="mb-8">
      <v-card-title class="syne-font text-h5 pa-6">Filtrēt preces</v-card-title>
      <v-card-text class="px-6 pb-6">
        <v-row>
          <v-col cols="12" md="3">
            <v-select
              v-model="filters.category"
              :items="categories"
              item-title="name"
              item-value="kategorijas_id"
              label="Kategorija"
              variant="outlined"
              density="comfortable"
              clearable
              @update:model-value="applyFilters"
            ></v-select>
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="filters.priceRange"
              :items="priceRanges"
              item-title="title"
              item-value="value"
              label="Cenu diapazons"
              variant="outlined"
              density="comfortable"
              clearable
              @update:model-value="applyFilters"
            ></v-select>
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="filters.store"
              :items="stores"
              item-title="name"
              item-value="veikala_id"
              label="Veikals"
              variant="outlined"
              density="comfortable"
              clearable
              @update:model-value="applyFilters"
            ></v-select>
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="filters.sortBy"
              :items="sortOptions"
              item-title="title"
              item-value="value"
              label="Kārtot pēc"
              variant="outlined"
              density="comfortable"
              @update:model-value="applyFilters"
            ></v-select>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-btn variant="text" color="secondary" @click="resetFilters">
              <v-icon start>mdi-refresh</v-icon>
              Atiestatīt filtrus
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <div v-if="productStore.loading" class="text-center py-12">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </div>

    <div v-else-if="productStore.error" class="text-center py-12">
      <v-alert type="error" variant="tonal">{{ productStore.error }}</v-alert>
    </div>

    <v-row v-else>
      <v-col
        v-for="product in productStore.filteredProducts"
        :key="product.preces_id ?? product.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
      >
        <ProductCard :product="product" @track-price="openTrackingDialog" />
      </v-col>

      <v-col v-if="productStore.filteredProducts.length === 0" cols="12">
        <v-card elevation="0" class="text-center pa-12">
          <v-icon size="64" color="grey">mdi-package-variant</v-icon>
          <p class="text-h6 mt-4">Nav atrasta neviena prece</p>
          <p class="text-body-2 text-medium-emphasis">Mēģiniet mainīt filtrus vai meklēšanas kritērijus</p>
        </v-card>
      </v-col>
    </v-row>

    <PriceTrackingDialog ref="trackingDialogRef" />
  </v-container>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useProductStore } from '@/stores/products'
import ProductCard from '@/components/products/ProductCard.vue'
import PriceTrackingDialog from '@/components/tracking/PriceTrackingDialog.vue'
import api from '@/services/api'

const productStore = useProductStore()

const categories = ref([])
const stores = ref([])

const priceRanges = [
  { title: '€0 - €200', value: '0-200' },
  { title: '€200 - €500', value: '200-500' },
  { title: '€500 - €1000', value: '500-1000' },
  { title: '€1000+', value: '1000+' }
]

const sortOptions = [
  { title: 'Cena: Zemākā - Augstākā', value: 'price-asc' },
  { title: 'Cena: Augstākā - Zemākā', value: 'price-desc' },
  { title: 'Nosaukums', value: 'name' },
  { title: 'Jaunākās', value: 'newest' }
]

const filters = reactive({
  category: '',
  priceRange: '',
  store: '',
  sortBy: 'price-asc'
})

const trackingDialogRef = ref(null)

const applyFilters = () => {
  Object.keys(filters).forEach(key => productStore.setFilter(key, filters[key]))
}

const resetFilters = () => {
  filters.category = ''
  filters.priceRange = ''
  filters.store = ''
  filters.sortBy = 'price-asc'
  productStore.resetFilters()
}

const openTrackingDialog = (product) => {
  trackingDialogRef.value?.open(product)
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = response.data.data ?? []
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchStores = async () => {
  try {
    const response = await api.get('/stores')
    stores.value = response.data.data ?? []
  } catch (error) {
    console.error('Error fetching stores:', error)
  }
}

onMounted(async () => {
  await Promise.all([
    productStore.fetchProducts(),
    fetchCategories(),
    fetchStores()
  ])
})
</script>

<style scoped>
.products-view {
  background: linear-gradient(to bottom, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}
.hero-section { animation: fadeInUp 0.8s ease-out; }
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
