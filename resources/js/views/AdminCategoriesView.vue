<template>
  <v-container class="py-6">
    <h1 class="text-h4 mb-4">Administrācija — kategorijas</h1>

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
          Pievienot kategoriju
        </v-btn>
      </div>

      <v-table v-if="categories.length > 0" class="elevation-1 rounded">
        <thead>
          <tr>
            <th>Nosaukums</th>
            <th>Vecākā kategorija</th>
            <th>Apraksts</th>
            <th>Preču skaits</th>
            <th class="text-right">Darbības</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cat in categories" :key="cat.kategorijas_id">
            <td>{{ cat.nosaukums }}</td>
            <td>{{ cat.parent?.nosaukums ?? '—' }}</td>
            <td>{{ cat.apraksts || '—' }}</td>
            <td>{{ cat.products_count ?? 0 }}</td>
            <td class="text-right">
              <v-btn size="small" variant="text" color="primary" @click="openEditDialog(cat)">Rediģēt</v-btn>
              <v-btn
                size="small"
                variant="text"
                color="error"
                :disabled="(cat.products_count ?? 0) > 0"
                @click="confirmDelete(cat)"
              >
                Dzēst
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>

      <v-alert v-else-if="!loading" type="info" variant="tonal">
        Kategoriju nav. Pievienojiet pirmo.
      </v-alert>

      <!-- Rediģēt / Pievienot kategoriju -->
      <v-dialog v-model="editDialog" max-width="500" persistent>
        <v-card>
          <v-card-title>{{ editingCategory ? 'Rediģēt kategoriju' : 'Pievienot kategoriju' }}</v-card-title>
          <v-card-text>
            <v-form ref="formRef">
              <v-text-field
                v-model="form.nosaukums"
                label="Nosaukums"
                variant="outlined"
                density="comfortable"
                :rules="[v => !!v?.trim() || 'Nosaukums ir obligāts']"
              ></v-text-field>
              <v-textarea
                v-model="form.apraksts"
                label="Apraksts"
                variant="outlined"
                density="comfortable"
                rows="2"
              ></v-textarea>
              <v-select
                v-model="form.vecaka_kategorijas_id"
                :items="parentOptions"
                item-title="nosaukums"
                item-value="kategorijas_id"
                label="Vecākā kategorija (neobligāta)"
                variant="outlined"
                density="comfortable"
                clearable
              ></v-select>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="editDialog = false">Atcelt</v-btn>
            <v-btn color="primary" :loading="saving" @click="saveCategory">Saglabāt</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Dzēst kategoriju -->
      <v-dialog v-model="deleteDialog" max-width="420" persistent>
        <v-card>
          <v-card-title>Dzēst kategoriju?</v-card-title>
          <v-card-text>
            Kategorija "{{ categoryToDelete?.nosaukums }}" tiks dzēsta. Šo darbību nevar atsaukt.
            <br />
            <span class="text-body-2 text-medium-emphasis" v-if="(categoryToDelete?.products_count ?? 0) > 0">
              Kategoriju ar pievienotām precēm nevar dzēst.
            </span>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="deleteDialog = false">Atcelt</v-btn>
            <v-btn
              color="error"
              :loading="deleting"
              :disabled="(categoryToDelete?.products_count ?? 0) > 0"
              @click="doDelete"
            >
              Dzēst
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </template>
  </v-container>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useSnackbarStore } from '@/stores/snackbar'
import api from '@/services/api'

const authStore = useAuthStore()
const snackbarStore = useSnackbarStore()

const loading = ref(true)
const saving = ref(false)
const deleting = ref(false)
const categories = ref([])
const editDialog = ref(false)
const deleteDialog = ref(false)
const editingCategory = ref(null)
const categoryToDelete = ref(null)
const formRef = ref(null)

const form = reactive({
  nosaukums: '',
  apraksts: '',
  vecaka_kategorijas_id: null
})

const parentOptions = computed(() => {
  if (!editingCategory.value) return categories.value
  return categories.value.filter(
    c => c.kategorijas_id !== editingCategory.value.kategorijas_id
  )
})

async function loadCategories() {
  try {
    const res = await api.get('/categories')
    categories.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
  } catch (err) {
    categories.value = []
    snackbarStore.showError('Neizdevās ielādēt kategorijas')
  }
}

function openEditDialog(category = null) {
  editingCategory.value = category
  form.nosaukums = category?.nosaukums ?? ''
  form.apraksts = category?.apraksts ?? ''
  form.vecaka_kategorijas_id = category?.vecaka_kategorijas_id ?? null
  editDialog.value = true
}

async function saveCategory() {
  const valid = form.nosaukums?.trim()
  if (!valid) {
    snackbarStore.showError('Nosaukums ir obligāts')
    return
  }
  saving.value = true
  try {
    const payload = {
      nosaukums: form.nosaukums.trim(),
      apraksts: form.apraksts?.trim() || null,
      vecaka_kategorijas_id: form.vecaka_kategorijas_id || null
    }
    if (editingCategory.value) {
      await api.put(`/categories/${editingCategory.value.kategorijas_id}`, payload)
      snackbarStore.showSuccess('Kategorija atjaunināta')
    } else {
      await api.post('/categories', payload)
      snackbarStore.showSuccess('Kategorija pievienota')
    }
    editDialog.value = false
    await loadCategories()
  } catch (err) {
    const msg = err.response?.data?.message || 'Neizdevās saglabāt kategoriju'
    snackbarStore.showError(msg)
  } finally {
    saving.value = false
  }
}

function confirmDelete(category) {
  categoryToDelete.value = category
  deleteDialog.value = true
}

async function doDelete() {
  if (!categoryToDelete.value) return
  deleting.value = true
  try {
    await api.delete(`/categories/${categoryToDelete.value.kategorijas_id}`)
    snackbarStore.showSuccess('Kategorija dzēsta')
    deleteDialog.value = false
    categoryToDelete.value = null
    await loadCategories()
  } catch (err) {
    const msg = err.response?.data?.message || 'Neizdevās dzēst kategoriju'
    snackbarStore.showError(msg)
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  if (authStore.isAdmin) {
    loading.value = true
    await loadCategories()
    loading.value = false
  }
})
</script>

