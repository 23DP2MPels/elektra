<template>
  <v-container class="py-8">
    <v-row justify="center">
      <v-col cols="12" md="8">
        <h1 class="text-h4 mb-6">Profila iestatījumi</h1>

        <template v-if="!authStore.isAuthenticated">
          <v-alert type="info" variant="tonal">
            Lai piekļūtu profila iestatījumiem, lūdzu,
            <router-link to="/login" class="font-weight-bold">
              pieslēdzieties
            </router-link>.
          </v-alert>
        </template>

        <template v-else>
          <v-card elevation="2" rounded="lg" class="mb-6">
            <v-card-title class="text-h6">
              Pamatdati
            </v-card-title>
            <v-card-text>
              <v-form @submit.prevent="saveProfile" ref="profileFormRef">
                <v-text-field
                  v-model="vards"
                  label="Vārds"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.vards"
                  autocomplete="name"
                  class="mb-3"
                ></v-text-field>
                <v-text-field
                  v-model="epasts"
                  label="E-pasts"
                  type="email"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.epasts"
                  autocomplete="email"
                  class="mb-3"
                ></v-text-field>
                <v-switch
                  v-model="sanemt_pazinojumus"
                  label="Saņemt paziņojumus par cenu izmaiņām un labojumiem"
                  color="primary"
                  hide-details
                  class="mb-4"
                ></v-switch>

                <div class="d-flex justify-end">
                  <v-btn
                    type="submit"
                    color="primary"
                    :loading="authStore.loading"
                  >
                    Saglabāt profilu
                  </v-btn>
                </div>
              </v-form>
            </v-card-text>
          </v-card>

          <v-card elevation="2" rounded="lg">
            <v-card-title class="text-h6">
              Paroles maiņa
            </v-card-title>
            <v-card-text>
              <v-form @submit.prevent="changePassword" ref="passwordFormRef">
                <v-text-field
                  v-model="current_password"
                  label="Pašreizējā parole"
                  type="password"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.current_password"
                  autocomplete="current-password"
                  class="mb-3"
                ></v-text-field>
                <v-text-field
                  v-model="parole"
                  label="Jaunā parole"
                  type="password"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.parole"
                  autocomplete="new-password"
                  class="mb-3"
                ></v-text-field>
                <v-text-field
                  v-model="parole_confirmation"
                  label="Jaunā parole atkārtoti"
                  type="password"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.parole_confirmation"
                  autocomplete="new-password"
                  class="mb-4"
                ></v-text-field>

                <div class="d-flex justify-end">
                  <v-btn
                    type="submit"
                    color="primary"
                    :loading="authStore.loading"
                  >
                    Nomainīt paroli
                  </v-btn>
                </div>
              </v-form>
            </v-card-text>
          </v-card>
        </template>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useSnackbarStore } from '@/stores/snackbar'

const authStore = useAuthStore()
const snackbarStore = useSnackbarStore()

const vards = ref('')
const epasts = ref('')
const sanemt_pazinojumus = ref(true)

const current_password = ref('')
const parole = ref('')
const parole_confirmation = ref('')

const profileFormRef = ref(null)
const passwordFormRef = ref(null)

const errors = reactive({
  vards: [],
  epasts: [],
  current_password: [],
  parole: [],
  parole_confirmation: []
})

function fillFromUser(user) {
  if (!user) return
  vards.value = user.vards || user.name || ''
  epasts.value = user.epasts || user.email || ''
  sanemt_pazinojumus.value =
    typeof user.sanemt_pazinojumus === 'boolean'
      ? user.sanemt_pazinojumus
      : true
}

onMounted(() => {
  if (authStore.user) {
    fillFromUser(authStore.user)
  }
})

watch(
  () => authStore.user,
  (newUser) => {
    fillFromUser(newUser)
  },
  { immediate: false }
)

function resetErrors() {
  errors.vards = []
  errors.epasts = []
  errors.current_password = []
  errors.parole = []
  errors.parole_confirmation = []
}

async function saveProfile() {
  resetErrors()

  if (!vards.value?.trim()) {
    errors.vards = ['Vārds ir obligāts']
    return
  }
  if (!epasts.value?.trim()) {
    errors.epasts = ['E-pasts ir obligāts']
    return
  }

  try {
    await authStore.updateProfile({
      vards: vards.value.trim(),
      epasts: epasts.value.trim(),
      sanemt_pazinojumus: !!sanemt_pazinojumus.value
    })
    snackbarStore.showSuccess('Profils atjaunināts veiksmīgi')
  } catch (err) {
    const data = err.response?.data
    if (data?.errors) {
      Object.keys(data.errors).forEach(key => {
        if (errors[key] !== undefined) {
          errors[key] = [].concat(data.errors[key])
        }
      })
    } else if (data?.message) {
      errors.epasts = [data.message]
    }
  }
}

async function changePassword() {
  resetErrors()

  if (!current_password.value) {
    errors.current_password = ['Lūdzu, ievadiet pašreizējo paroli']
    return
  }
  if (!parole.value) {
    errors.parole = ['Jaunā parole ir obligāta']
    return
  }
  if (parole.value.length < 8) {
    errors.parole = ['Parolei jābūt vismaz 8 rakstzīmēm']
    return
  }
  if (parole.value !== parole_confirmation.value) {
    errors.parole_confirmation = ['Paroles nesakrīt']
    return
  }

  try {
    await authStore.updateProfile({
      current_password: current_password.value,
      parole: parole.value,
      parole_confirmation: parole_confirmation.value
    })
    snackbarStore.showSuccess('Parole veiksmīgi nomainīta')
    current_password.value = ''
    parole.value = ''
    parole_confirmation.value = ''
  } catch (err) {
    const data = err.response?.data
    if (data?.errors) {
      Object.keys(data.errors).forEach(key => {
        if (errors[key] !== undefined) {
          errors[key] = [].concat(data.errors[key])
        }
      })
    } else if (data?.message) {
      errors.current_password = [data.message]
    }
  }
}
</script>

