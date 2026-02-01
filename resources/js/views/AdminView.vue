<template>
  <v-container class="py-6">
    <h1 class="text-h4 mb-4">Administrācija — preces</h1>

    <template v-if="!authStore.isAuthenticated">
      <v-alert type="warning" variant="tonal">
        Lūdzu, <router-link to="/login" class="font-weight-bold">pieslēdzieties</router-link>, lai piekļūtu administrācijai.
      </v-alert>
    </template>

    <template v-else-if="!authStore.isAdmin">
      <v-alert type="error" variant="tonal">
        Šai lapai ir piekļuve tikai administratoriem.
      </v-alert>
    </template>

    <template v-else>
      <v-progress-linear v-if="loading" indeterminate color="primary" class="mb-4"></v-progress-linear>

      <div class="d-flex align-center gap-2 mb-4">
        <v-btn color="primary" @click="openEditDialog()">
          <v-icon start>mdi-plus</v-icon>
          Pievienot preci
        </v-btn>
      </div>

      <v-table v-if="products.length > 0" class="elevation-1 rounded">
        <thead>
          <tr>
            <th>Nosaukums</th>
            <th>Kategorija</th>
            <th>Cena</th>
            <th class="text-right">Darbības</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in products" :key="p.preces_id ?? p.id">
            <td>{{ p.nosaukums ?? p.name }}</td>
            <td>{{ p.category?.nosaukums ?? p.category?.name ?? '—' }}</td>
            <td>{{ p.price ?? p.current_price != null ? '€' + formatPrice(p.price ?? p.current_price) : '—' }}</td>
            <td class="text-right">
              <v-btn size="small" variant="text" color="primary" @click="openEditDialog(p)">Rediģēt</v-btn>
              <v-btn size="small" variant="text" color="error" @click="confirmDelete(p)">Dzēst</v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>

      <v-alert v-else-if="!loading" type="info" variant="tonal">Preču nav. Pievienojiet pirmo.</v-alert>

      <!-- Rediģēt / Pievienot preci -->
      <v-dialog v-model="editDialog" max-width="600" persistent>
        <v-card>
          <v-card-title>{{ editingProduct ? 'Rediģēt preci' : 'Pievienot preci' }}</v-card-title>
          <v-card-text>
            <v-form ref="formRef">
              <v-text-field v-model="form.nosaukums" label="Nosaukums" variant="outlined" density="comfortable" :rules="[v => !!v?.trim() || 'Obligāts']"></v-text-field>
              <v-textarea v-model="form.apraksts" label="Apraksts" variant="outlined" density="comfortable" rows="2"></v-textarea>
              <v-text-field v-model="form.razotajs" label="Ražotājs" variant="outlined" density="comfortable"></v-text-field>
              <v-text-field v-model="form.modelis" label="Modelis" variant="outlined" density="comfortable"></v-text-field>
              <v-text-field v-model="form.attels_url" label="Attēla URL" variant="outlined" density="comfortable"></v-text-field>
              <v-select
                v-model="form.kategorijas_id"
                :items="categories"
                item-title="nosaukums"
                item-value="kategorijas_id"
                label="Kategorija"
                variant="outlined"
                density="comfortable"
                :rules="[v => v != null && v !== '' || 'Izvēlieties kategoriju']"
              ></v-select>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="editDialog = false">Atcelt</v-btn>
            <v-btn color="primary" :loading="saving" @click="saveProduct">Saglabāt</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Dzēst apstiprinājums -->
      <v-dialog v-model="deleteDialog" max-width="400" persistent>
        <v-card>
          <v-card-title>Dzēst preci?</v-card-title>
          <v-card-text>
            Prece "{{ productToDelete?.nosaukums ?? productToDelete?.name }}" tiks dzēsta. Šo darbību nevar atsaukt.
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="deleteDialog = false">Atcelt</v-btn>
            <v-btn color="error" :loading="deleting" @click="doDelete">Dzēst</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </template>
  </v-container>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useSnackbarStore } from '@/stores/snackbar'
import api from '@/services/api'
import { formatPrice } from '@/utils/price'

const authStore = useAuthStore()
const snackbarStore = useSnackbarStore()

const loading = ref(true)
const saving = ref(false)
const deleting = ref(false)
const products = ref([])
const categories = ref([])
const editDialog = ref(false)
const deleteDialog = ref(false)
const editingProduct = ref(null)
const productToDelete = ref(null)
const formRef = ref(null)

const form = reactive({
  nosaukums: '',
  apraksts: '',
  razotajs: '',
  modelis: '',
  attels_url: '',
  kategorijas_id: null
})

async function loadProducts() {
  try {
    const res = await api.get('/products', { params: { per_page: 500 } })
    products.value = res.data?.data ?? []
  } catch (err) {
    snackbarStore.showError('Neizdevās ielādēt preces')
    products.value = []
  }
}

async function loadCategories() {
  try {
    const res = await api.get('/categories')
    categories.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
  } catch (err) {
    categories.value = []
  }
}

function openEditDialog(product = null) {
  editingProduct.value = product
  form.nosaukums = product?.nosaukums ?? product?.name ?? ''
  form.apraksts = product?.apraksts ?? ''
  form.razotajs = product?.razotajs ?? ''
  form.modelis = product?.modelis ?? ''
  form.attels_url = product?.attels_url ?? product?.image_url ?? ''
  form.kategorijas_id = product?.kategorijas_id ?? product?.category?.kategorijas_id ?? (categories.value[0]?.kategorijas_id ?? null)
  editDialog.value = true
}

async function saveProduct() {
  const valid = form.nosaukums?.trim()
  if (!valid) {
    snackbarStore.showError('Nosaukums ir obligāts')
    return
  }
  if (form.kategorijas_id == null || form.kategorijas_id === '') {
    snackbarStore.showError('Izvēlieties kategoriju')
    return
  }
  saving.value = true
  try {
    const payload = {
      nosaukums: form.nosaukums.trim(),
      apraksts: form.apraksts?.trim() || null,
      razotajs: form.razotajs?.trim() || null,
      modelis: form.modelis?.trim() || null,
      attels_url: form.attels_url?.trim() || null,
      kategorijas_id: form.kategorijas_id
    }
    if (editingProduct.value) {
      const id = editingProduct.value.preces_id ?? editingProduct.value.id
      await api.put(`/products/${id}`, payload)
      snackbarStore.showSuccess('Prece atjaunināta')
    } else {
      await api.post('/products', payload)
      snackbarStore.showSuccess('Prece pievienota')
    }
    editDialog.value = false
    await loadProducts()
  } catch (err) {
    const msg = err.response?.data?.message || err.response?.data?.errors?.nosaukums?.[0] || 'Kļūda'
    snackbarStore.showError(msg)
  } finally {
    saving.value = false
  }
}

function confirmDelete(product) {
  productToDelete.value = product
  deleteDialog.value = true
}

async function doDelete() {
  if (!productToDelete.value) return
  const id = productToDelete.value.preces_id ?? productToDelete.value.id
  deleting.value = true
  try {
    await api.delete(`/products/${id}`)
    snackbarStore.showSuccess('Prece dzēsta')
    deleteDialog.value = false
    productToDelete.value = null
    await loadProducts()
  } catch (err) {
    snackbarStore.showError(err.response?.data?.message || 'Neizdevās dzēst')
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  if (authStore.isAdmin) {
    loading.value = true
    await Promise.all([loadProducts(), loadCategories()])
    loading.value = false
  }
})
</script>
