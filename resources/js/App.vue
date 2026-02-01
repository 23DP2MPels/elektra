<template>
  <v-app>
    <AppHeader />
    <v-main>
      <router-view />
    </v-main>
    <ComparisonPanel v-model="showComparisonPanel" />
    <PriceTrackingDialog />
    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      :timeout="3000"
      top
      right
    >
      {{ snackbar.message }}
      <template v-slot:actions>
        <v-btn variant="text" @click="snackbar.show = false">Aizvērt</v-btn>
      </template>
    </v-snackbar>
  </v-app>
</template>

<script setup>
import { computed, ref, provide } from 'vue'
import { useSnackbarStore } from '@/stores/snackbar'
import { useProductStore } from '@/stores/products'
import AppHeader from '@/components/layout/AppHeader.vue'
import ComparisonPanel from '@/components/comparison/ComparisonPanel.vue'
import PriceTrackingDialog from '@/components/tracking/PriceTrackingDialog.vue'

const snackbarStore = useSnackbarStore()
const productStore = useProductStore()
const snackbar = computed(() => snackbarStore.snackbar)

const showComparisonPanel = ref(false)
provide('comparisonCount', computed(() => productStore.selectedProducts.length))
provide('openComparisonPanel', () => { showComparisonPanel.value = true })
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@400;500;700&display=swap');
:root {
  --primary: #FF6B35;
  --secondary: #004E89;
  --accent: #FFD23F;
}
.v-application {
  font-family: 'DM Sans', sans-serif !important;
}
.syne-font {
  font-family: 'Syne', sans-serif !important;
}
</style>
