export const formatPrice = (value) => {
    const num = Number(value)
    return Number.isFinite(num) ? num.toFixed(2) : '—'
  }