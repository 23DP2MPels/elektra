<template>
  <v-bottom-sheet :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" max-width="100%" persistent>
    <v-card class="comparison-panel" elevation="24">
      <v-card-title class="d-flex align-center justify-space-between pa-6">
        <div class="syne-font text-h4">
          Salīdzināt preces ({{ productStore.comparedProducts.length }})
        </div>
        <v-btn icon="mdi-close" variant="text" @click="closePanel"></v-btn>
      </v-card-title>

      <v-card-text v-if="productStore.comparedProducts.length > 0" class="pa-6">
        <v-row>
          <v-col
            v-for="product in productStore.comparedProducts"
            :key="product.preces_id ?? product.id"
            cols="12"
            md="6"
            lg="3"
          >
            <v-card elevation="0" color="grey-lighten-4" rounded="lg">
              <v-img :src="product.image_url || placeholderImage" height="150" contain class="ma-4"></v-img>
              <v-card-text>
                <h4 class="syne-font mb-2">{{ product.name }}</h4>
                <div class="price text-primary syne-font mb-2">
                  €{{ formatPrice(product.current_price ?? product.price) }}
                </div>
                <div class="text-caption text-medium-emphasis">
                  <v-icon size="x-small">mdi-store</v-icon>
                  {{ product.store?.name }}
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <v-divider class="my-6"></v-divider>

        <div class="comparison-table">
          <v-table density="comfortable">
            <thead>
              <tr>
                <th class="font-weight-bold text-secondary">Parametrs</th>
                <th v-for="product in productStore.comparedProducts" :key="product.preces_id ?? product.id" class="text-center">
                  {{ product.name }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="spec in allSpecs" :key="spec">
                <td class="font-weight-bold text-secondary">{{ spec }}</td>
                <td v-for="product in productStore.comparedProducts" :key="product.preces_id ?? product.id" class="text-center">
                  {{ getSpecValue(product, spec) }}
                </td>
              </tr>
            </tbody>
          </v-table>
        </div>

        <v-card-actions class="px-0 pt-6">
          <v-btn variant="outlined" color="error" @click="clearAll">
            <v-icon start>mdi-delete</v-icon>
            Notīrīt visu
          </v-btn>
        </v-card-actions>
      </v-card-text>

      <v-card-text v-else class="text-center pa-8">
        <v-icon size="64" color="grey">mdi-compare</v-icon>
        <p class="text-h6 mt-4">Nav izvēlētas preces salīdzināšanai</p>
        <p class="text-body-2 text-medium-emphasis">Atlasiet līdz 4 precēm, lai salīdzinātu to specifikācijas</p>
      </v-card-text>
    </v-card>
  </v-bottom-sheet>
</template>

<script setup>
import { computed } from 'vue'
import { useProductStore } from '@/stores/products'
import { formatPrice } from '@/utils/price'

const props = defineProps({
  modelValue: { type: Boolean, default: false }
})
const emit = defineEmits(['update:modelValue'])

const productStore = useProductStore()

const placeholderImage = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f0f0f0" width="200" height="200"/%3E%3C/svg%3E'

const allSpecs = computed(() => {
  const specs = new Set()
  productStore.comparedProducts.forEach(product => {
    (product.specifications || []).forEach(spec => specs.add(spec.parametrs))
  })
  return Array.from(specs)
})

const getSpecValue = (product, specName) => {
  if (!product.specifications) return ' ' /* N/A */
  const spec = product.specifications.find(s => s.parametrs === specName)
  return spec ? spec.vertiba : ' ' /* N/A */
}

const closePanel = () => emit('update:modelValue', false)
const clearAll = () => {
  productStore.clearComparison()
  emit('update:modelValue', false)
}
</script>

<style scoped>
.comparison-panel { max-height: 80vh; overflow-y: auto; }
.price { font-size: 1.5rem; font-weight: 800; }
.comparison-table { overflow-x: auto; }
</style>
