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
              <v-text-field
                v-model="form.cena"
                label="Cena (€)"
                variant="outlined"
                density="comfortable"
                type="number"
                step="0.01"
              ></v-text-field>
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
              <v-select
                v-model="form.veikala_id"
                :items="stores"
                item-title="nosaukums"
                item-value="veikala_id"
                label="Veikals"
                variant="outlined"
                density="comfortable"
                :rules="[v => v != null && v !== '' || 'Izvēlieties veikalu']"
              ></v-select>

              <v-divider class="my-4"></v-divider>
              <div class="mb-2 d-flex align-center justify-space-between">
                <span class="text-subtitle-1 font-weight-medium">Specifikācijas</span>
                <v-btn size="small" variant="text" color="primary" @click="addSpecRow">
                  <v-icon start>mdi-plus</v-icon>
                  Pievienot parametru
                </v-btn>
              </div>
              <v-row v-for="(spec, index) in form.specs" :key="index" class="mb-2" align="center">
                <v-col cols="5">
                  <v-text-field
                    v-model="spec.parametrs"
                    label="Parametrs (piem., &quot;Ekrāna izmērs&quot;)"
                    variant="outlined"
                    density="comfortable"
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="spec.vertiba"
                    label="Vērtība (piem., &quot;6.7&quot;)"
                    variant="outlined"
                    density="comfortable"
                  ></v-text-field>
                </v-col>
                <v-col cols="1" class="text-right">
                  <v-btn icon="mdi-delete" variant="text" color="error" @click="removeSpecRow(index)"></v-btn>
                </v-col>
              </v-row>
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
const stores = ref([])
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
  kategorijas_id: null,
  veikala_id: null,
  cena: '',
  specs: []
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

async function loadStores() {
  try {
    const res = await api.get('/stores')
    stores.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
  } catch (err) {
    stores.value = []
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
  form.veikala_id = product?.store?.veikala_id ?? (stores.value[0]?.veikala_id ?? null)
  form.cena = product?.price ?? product?.current_price ?? ''
  form.specs = Array.isArray(product?.specifications)
    ? product.specifications.map(s => ({
        parametrs: s.parametrs,
        vertiba: s.vertiba
      }))
    : []
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
  if (!form.cena || Number.isNaN(Number(form.cena))) {
    snackbarStore.showError('Ievadiet korektu cenu (piem., 199.99)')
    return
  }
  if (!form.veikala_id) {
    snackbarStore.showError('Izvēlieties veikalu')
    return
  }
  saving.value = true
  try {
    const specsPayload = Array.isArray(form.specs)
      ? form.specs
          .filter(s => s.parametrs?.trim() && s.vertiba?.trim())
          .map(s => ({
            parametrs: s.parametrs.trim(),
            vertiba: s.vertiba.trim()
          }))
      : []

    const payload = {
      nosaukums: form.nosaukums.trim(),
      apraksts: form.apraksts?.trim() || null,
      razotajs: form.razotajs?.trim() || null,
      modelis: form.modelis?.trim() || null,
      attels_url: form.attels_url?.trim() || null,
      kategorijas_id: form.kategorijas_id,
      veikala_id: form.veikala_id,
      cena: Number(form.cena),
      specs: specsPayload
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

function addSpecRow() {
  form.specs.push({ parametrs: '', vertiba: '' })
}

function removeSpecRow(index) {
  form.specs.splice(index, 1)
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
    await Promise.all([loadProducts(), loadCategories(), loadStores()])
    loading.value = false
  }
})
</script>

