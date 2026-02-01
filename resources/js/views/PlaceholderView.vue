<template>
  <v-container class="py-8">
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card elevation="1" rounded="lg" class="pa-6">
          <v-card-title class="text-h5">{{ title }}</v-card-title>
          <v-card-text>
            <p class="text-medium-emphasis">{{ description }}</p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const titles = {
  profile: 'Profils',
  'tracked-products': 'Sekotās preces',
  admin: 'Administrācija'
}

const descriptions = {
  profile: 'Šeit varēsiet rediģēt profila datus. Lapa tiek pilnveidota.',
  'tracked-products': 'Šeit tiks rādītas preces, kurām sekojat cenai. Lapa tiek pilnveidota.',
  admin: 'Administratora panelis — preču, kategoriju un veikalu pārvaldība. Lapa tiek pilnveidota.'
}

const title = computed(() => titles[route.name] || route.name || 'Lapa')
const description = computed(() => descriptions[route.name] || 'Šī lapa tiek pilnveidota.')
</script>
