import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useProductStore = defineStore('products', () => {
  const products = ref([])
  const selectedProducts = ref([])
  const loading = ref(false)
  const error = ref(null)
  const filters = ref({
    category: '',
    priceRange: '',
    store: '',
    sortBy: 'price-asc',
    search: ''
  })

  const filteredProducts = computed(() => {
    let filtered = [...products.value]
    if (filters.value.search) {
      const searchLower = filters.value.search.toLowerCase()
      filtered = filtered.filter(p =>
        (p.name || '').toLowerCase().includes(searchLower) ||
        (p.category?.name || '').toLowerCase().includes(searchLower)
      )
    }
    if (filters.value.category) {
      filtered = filtered.filter(p => p.kategorijas_id === filters.value.category)
    }
    if (filters.value.priceRange) {
      const [min, max] = filters.value.priceRange.split('-').map(Number)
      if (max) {
        filtered = filtered.filter(p => (p.price || p.current_price) >= min && (p.price || p.current_price) <= max)
      } else {
        filtered = filtered.filter(p => (p.price || p.current_price) >= min)
      }
    }
    if (filters.value.store) {
      filtered = filtered.filter(p => p.store?.veikala_id === filters.value.store)
    }
    switch (filters.value.sortBy) {
      case 'price-asc':
        filtered.sort((a, b) => (a.price || a.current_price || 0) - (b.price || b.current_price || 0))
        break
      case 'price-desc':
        filtered.sort((a, b) => (b.price || b.current_price || 0) - (a.price || a.current_price || 0))
        break
      case 'name':
        filtered.sort((a, b) => (a.name || '').localeCompare(b.name || ''))
        break
      case 'newest':
        filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        break
    }
    return filtered
  })

  const comparedProducts = computed(() => {
    return products.value.filter(p => selectedProducts.value.includes(p.preces_id || p.id))
  })

  async function fetchProducts() {
    loading.value = true
    error.value = null
    try {
      const response = await api.get('/products')
      products.value = response.data.data || []
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function fetchProduct(id) {
    loading.value = true
    error.value = null
    try {
      const response = await api.get(`/products/${id}`)
      return response.data.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  function toggleCompare(productId) {
    const index = selectedProducts.value.indexOf(productId)
    if (index > -1) {
      selectedProducts.value.splice(index, 1)
    } else {
      if (selectedProducts.value.length < 4) {
        selectedProducts.value.push(productId)
      } else {
        throw new Error('Maksimums 4 preces salīdzināšanai!')
      }
    }
  }

  function clearComparison() {
    selectedProducts.value = []
  }

  function setFilter(key, value) {
    filters.value[key] = value
  }

  function resetFilters() {
    filters.value = {
      category: '',
      priceRange: '',
      store: '',
      sortBy: 'price-asc',
      search: ''
    }
  }

  return {
    products,
    selectedProducts,
    loading,
    error,
    filters,
    filteredProducts,
    comparedProducts,
    fetchProducts,
    fetchProduct,
    toggleCompare,
    clearComparison,
    setFilter,
    resetFilters
  }
})
