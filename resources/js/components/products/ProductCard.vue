<template>
  <v-card class="product-card" elevation="2" :ripple="false">
    <div class="card-top-indicator"></div>
    <v-img
      :src="product.image_url || placeholderImage"
      height="200"
      contain
      class="product-image"
    ></v-img>

    <v-card-text>
      <v-chip size="small" color="secondary" variant="tonal" class="mb-2">
        {{ product.category?.name }}
      </v-chip>

      <h3 class="product-title syne-font mb-3">{{ product.name }}</h3>

      <v-list density="compact" class="mb-3 bg-transparent">
        <v-list-item v-for="(value, key) in displaySpecs" :key="key" class="px-0">
          <template v-slot:prepend>
            <span class="text-medium-emphasis text-caption">{{ key }}:</span>
          </template>
          <template v-slot:append>
            <span class="font-weight-bold text-body-2">{{ value }}</span>
          </template>
        </v-list-item>
      </v-list>

      <div class="d-flex align-center justify-space-between mb-3">
        <div>
          <div class="price syne-font text-primary">
            €{{ formattedPrice }}
          </div>
          <div class="text-caption text-medium-emphasis">
            <v-icon size="x-small">mdi-store</v-icon>
            {{ product.store?.name }}
          </div>
        </div>
      </div>
    </v-card-text>

    <v-card-actions class="px-4 pb-4">
      <v-btn
        :variant="isCompared ? 'flat' : 'outlined'"
        color="primary"
        block
        @click="toggleCompare"
      >
        <v-icon start>{{ isCompared ? 'mdi-check' : 'mdi-compare' }}</v-icon>
        Salīdzināt
      </v-btn>
    </v-card-actions>

    <v-card-actions class="px-4 pb-4 pt-0">
      <v-btn
        variant="outlined"
        color="secondary"
        block
        @click="trackPrice"
        :disabled="!authStore.isAuthenticated"
      >
        <v-icon start>mdi-bell-outline</v-icon>
        Sekot cenai
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { computed } from 'vue'
import { useProductStore } from '@/stores/products'
import { useAuthStore } from '@/stores/auth'
import { useSnackbarStore } from '@/stores/snackbar'
import { formatPrice } from '@/utils/price'

const props = defineProps({
  product: { type: Object, required: true }
})

const formattedPrice = computed(() => {
  const p = props.product.current_price ?? props.product.price
  return formatPrice(p)
})

const emit = defineEmits(['track-price'])

const productStore = useProductStore()
const authStore = useAuthStore()
const snackbarStore = useSnackbarStore()

const placeholderImage = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f0f0f0" width="200" height="200"/%3E%3C/svg%3E'

const isCompared = computed(() =>
  productStore.selectedProducts.includes(props.product.preces_id ?? props.product.id)
)

const displaySpecs = computed(() => {
  if (!props.product.specifications) return {}
  const specs = {}
  props.product.specifications.slice(0, 4).forEach(spec => {
    specs[spec.parametrs] = spec.vertiba
  })
  return specs
})

const toggleCompare = () => {
  try {
    productStore.toggleCompare(props.product.preces_id ?? props.product.id)
  } catch (error) {
    snackbarStore.showWarning(error.message)
  }
}

const trackPrice = () => {
  if (!authStore.isAuthenticated) {
    snackbarStore.showWarning('Lūdzu, pieslēdzieties, lai sekotu cenām')
    return
  }
  emit('track-price', props.product)
}
</script>

<style scoped>
.product-card { position: relative; overflow: hidden; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.product-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important; }
.card-top-indicator {
  position: absolute; top: 0; left: 0; right: 0; height: 4px;
  background: linear-gradient(90deg, #FF6B35, #FFD23F);
  transform: scaleX(0); transition: transform 0.4s ease; transform-origin: left; z-index: 1;
}
.product-card:hover .card-top-indicator { transform: scaleX(1); }
.product-title { font-size: 1.25rem; font-weight: 700; line-height: 1.3; color: #1A1A2E; }
.price { font-size: 2rem; font-weight: 800; }
</style>
