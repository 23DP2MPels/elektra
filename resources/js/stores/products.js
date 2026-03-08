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

  function normalize(str) {
    return (str || '')
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9āčēģīķļņōŗšūž\s]/g, ' ')
  }

  function makeChunks(str, size = 3) {
    const s = normalize(str).replace(/\s+/g, ' ').trim()
    if (s.length <= size) return new Set(s ? [s] : [])
    const out = new Set()
    for (let i = 0; i <= s.length - size; i++) {
      out.add(s.slice(i, i + size))
    }
    return out
  }

  function fuzzyMatch(text, query) {
    const normText = normalize(text)
    const normQuery = normalize(query)
    if (!normQuery) return true
    if (normText.includes(normQuery)) return true

    const textChunks = makeChunks(normText)
    const queryChunks = makeChunks(normQuery)
    if (queryChunks.size === 0) return true

    let matches = 0
    queryChunks.forEach(chunk => {
      if (textChunks.has(chunk)) matches++
    })

    const ratio = matches / queryChunks.size
    return ratio >= 0.4
  }

  const filteredProducts = computed(() => {
    let filtered = [...products.value]
    if (filters.value.search) {
      const query = filters.value.search
      filtered = filtered.filter(p =>
        fuzzyMatch(p.name || '', query) ||
        fuzzyMatch(p.category?.name || p.category?.nosaukums || '', query)
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
      return
    }

    const product = products.value.find(
      p => (p.preces_id || p.id) === productId
    )

    if (!product) {
      return
    }

    if (selectedProducts.value.length > 0) {
      const firstProduct = products.value.find(
        p => (p.preces_id || p.id) === selectedProducts.value[0]
      )

      if (
        firstProduct &&
        firstProduct.kategorijas_id &&
        product.kategorijas_id &&
        firstProduct.kategorijas_id !== product.kategorijas_id
      ) {
        throw new Error('Var salīdzināt tikai preces no vienas apakškategorijas.')
      }
    }

    if (selectedProducts.value.length < 4) {
      selectedProducts.value.push(productId)
    } else {
      throw new Error('Maksimums 4 preces salīdzināšanai!')
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
