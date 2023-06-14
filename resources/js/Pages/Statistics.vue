<script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import { Head } from '@inertiajs/vue3';
  import { onMounted } from 'vue';
  import Chart from 'chart.js/auto'

  onMounted(async () => {
    await axios.get('/api/accident/statistics')
    .then(res=>{
        const data = {
        labels: [
            'Resolved In One Hour',
            'Resolved In Two Hours',
            'Resolved In Three Hours',
            'Resolved In More Than Three Hours'
        ],
        datasets: [{
            data: Object.values(res.data),
            backgroundColor: [
            'rgb(54, 192, 135)',
            'rgb(25, 25, 255)',
            'rgb(255, 205, 86)',
            'rgb(255, 99, 132)'
            ],
            hoverOffset: 4
        }]
      };
      const ctx = document.getElementById('myChart');
        new Chart(ctx, {
        type: 'pie',
        data: data,
        options: {
            scales: {
            y: {
            beginAtZero: true
            }
        }
        }
    });
    })
    .catch(err=>{
      
    })
  })
</script>
<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Statistics</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="h-96 overflow-hidden flex justify-center">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>