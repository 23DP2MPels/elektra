<template>
  <v-container class="py-8">
    <v-row justify="center">
      <v-col cols="12" sm="8" md="5">
        <v-card elevation="2" rounded="lg">
          <v-card-title class="text-h5 pa-4">Reģistrēties</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="handleRegister" ref="formRef">
              <v-text-field
                v-model="vards"
                label="Vārds"
                variant="outlined"
                density="comfortable"
                :error-messages="errors.vards"
                autocomplete="name"
              ></v-text-field>
              <v-text-field
                v-model="epasts"
                label="E-pasts"
                type="email"
                variant="outlined"
                density="comfortable"
                :error-messages="errors.epasts"
                autocomplete="email"
              ></v-text-field>
              <v-text-field
                v-model="parole"
                label="Parole"
                type="password"
                variant="outlined"
                density="comfortable"
                :error-messages="errors.parole"
                autocomplete="new-password"
              ></v-text-field>
              <v-text-field
                v-model="parole_confirmation"
                label="Parole atkārtoti"
                type="password"
                variant="outlined"
                density="comfortable"
                :error-messages="errors.parole_confirmation"
                autocomplete="new-password"
              ></v-text-field>
              <v-btn
                type="submit"
                color="primary"
                block
                size="large"
                :loading="authStore.loading"
                class="mt-2"
              >
                Reģistrēties
              </v-btn>
            </v-form>
          </v-card-text>
          <v-card-actions class="px-4 pb-4">
            <v-spacer></v-spacer>
            <router-link to="/login" class="text-primary text-decoration-none">
              Jau ir konts? Pieslēgties
            </router-link>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSnackbarStore } from '@/stores/snackbar'

const router = useRouter()
const authStore = useAuthStore()
const snackbarStore = useSnackbarStore()

const vards = ref('')
const epasts = ref('')
const parole = ref('')
const parole_confirmation = ref('')
const formRef = ref(null)
const errors = reactive({
  vards: [],
  epasts: [],
  parole: [],
  parole_confirmation: []
})

const handleRegister = async () => {
  errors.vards = []
  errors.epasts = []
  errors.parole = []
  errors.parole_confirmation = []
  if (!vards.value?.trim()) {
    errors.vards = ['Vārds ir obligāts']
    return
  }
  if (!epasts.value?.trim()) {
    errors.epasts = ['E-pasts ir obligāts']
    return
  }
  if (!parole.value) {
    errors.parole = ['Parole ir obligāta']
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
    await authStore.register({
      vards: vards.value.trim(),
      epasts: epasts.value.trim(),
      parole: parole.value,
      parole_confirmation: parole_confirmation.value
    })
    snackbarStore.showSuccess('Reģistrācija veiksmīga')
    router.push('/products')
  } catch (err) {
    const data = err.response?.data
    if (data?.errors) {
      Object.keys(data.errors).forEach(k => {
        if (errors[k] !== undefined) errors[k] = [].concat(data.errors[k])
      })
    } else {
      errors.epasts = [data?.message || 'Reģistrācija neizdevās']
    }
  }
}
</script>
