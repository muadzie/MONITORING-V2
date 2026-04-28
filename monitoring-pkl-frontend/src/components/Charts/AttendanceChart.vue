<template>
  <div>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { Chart, registerables } from 'chart.js'
Chart.register(...registerables)

const props = defineProps({
  data: {
    type: Object,
    default: () => ({ present: 0, late: 0, absent: 0, sick: 0, permit: 0 })
  }
})

const chartCanvas = ref(null)
let chartInstance = null

const createChart = () => {
  if (chartInstance) {
    chartInstance.destroy()
  }
  
  if (chartCanvas.value) {
    chartInstance = new Chart(chartCanvas.value, {
      type: 'doughnut',
      data: {
        labels: ['Hadir', 'Terlambat', 'Alpha', 'Sakit', 'Izin'],
        datasets: [{
          data: [
            props.data.present || 0,
            props.data.late || 0,
            props.data.absent || 0,
            props.data.sick || 0,
            props.data.permit || 0
          ],
          backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#3B82F6', '#A855F7'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    })
  }
}

onMounted(() => {
  createChart()
})

watch(() => props.data, () => {
  createChart()
}, { deep: true })
</script>