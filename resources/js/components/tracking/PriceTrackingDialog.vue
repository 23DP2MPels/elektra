<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card rounded="lg">
      <v-card-title class="syne-font text-h5 pa-6">Sekot cenu izmaiņām</v-card-title>

      <v-card-text class="px-6 pb-4">
        <p class="mb-4">Vai vēlaties saņemt paziņojumus par šīs preces cenu samazinājumu?</p>

        <v-alert v-if="selectedProduct" type="info" variant="tonal" class="mb-4">
          <div class="font-weight-bold">{{ selectedProduct.name }}</div>
          <div class="text-caption">
            Pašreizējā cena: €{{ formatPrice(selectedProduct.current_price ?? selectedProduct.price) }}
          </div>
        </v-alert>

        <v-checkbox v-model="notifyByEmail" label="Paziņot pa e-pastu" hide-details class="mb-2"></v-checkbox>

        <v-text-field
          v-model="targetPrice"
          label="Vēlamā cena (neobligāti)"
          type="number"
          prefix="€"
          variant="outlined"
          density="comfortable"
          hint="Saņemt paziņojumu, kad cena nokrītas zem šīs summas"
          persistent-hint
        ></v-text-field>
      </v-card-text>

      <v-card-actions class="px-6 pb-6">
        <v-spacer></v-spacer>
        <v-btn variant="text" @click="close">Atcelt</v-btn>
        <v-btn color="primary" variant="flat" :loading="loading" @click="confirm">
          <v-icon start>mdi-bell</v-icon>
          Sekot
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/services/api'
import { useSnackbarStore } from '@/stores/snackbar'
import { formatPrice } from '@/utils/price'

const snackbarStore = useSnackbarStore()

const dialog = ref(false)
const selectedProduct = ref(null)
const notifyByEmail = ref(true)
const targetPrice = ref('')
const loading = ref(false)

const open = (product) => {
  selectedProduct.value = product
  dialog.value = true
  targetPrice.value = ''
}

const close = () => {
  dialog.value = false
  selectedProduct.value = null
}

const confirm = async () => {
  if (!selectedProduct.value) return
  loading.value = true
  try {
    await api.post('/tracked-products', {
      product_id: selectedProduct.value.preces_id ?? selectedProduct.value.id,
      target_price: targetPrice.value || null
    })
    snackbarStore.showSuccess('Prece pievienota sekošanas sarakstam!')
    close()
  } catch (error) {
    snackbarStore.showError(error.response?.data?.message || 'Kļūda pievienojot preci sekošanai')
  } finally {
    loading.value = false
  }
}

defineExpose({ open })
</script>
