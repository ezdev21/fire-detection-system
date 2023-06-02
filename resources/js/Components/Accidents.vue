<script setup>
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import axios from 'axios';
import { onMounted } from 'vue';
import { ref } from 'vue';

let accidents = ref([])
let modalOpen = ref(false)
let openedAccident = ref({})

onMounted(() => {
    fetchAccidents()
    listen()
})

const fetchAccidents = ()=>{
    axios.get('/api/accident')
    .then(res=>{
      accidents.value=res.data
    })
}

const listen = ()=>{
    Echo.private('accident')
    .listen('NewAccident',(accident)=>{
        accident.broadcasted=true
        accidents.value.push(accident)
        toast.info(`New Accident Occured At ${accident.latitude},${accident.longitude}`,{
        autoClose: 60000,
        toastStyle: {
          color: 'white',
          backgroundColor:'red'
    }});
    }) 
}

const complete = (id) =>{
    axios.post(`/api/${id}/complete`)
    .then(res=>{
       accidents.value=accidents.value.filter(ac=>ac.id!=id);
    })
    .catch(err=>{

    })
}

const remove = (id) =>{
    axios.delete(`/api/${id}/delete`)
    .then(res=>{
        accidents.value=accidents.value.filter(ac=>ac.id!=id);
    })
    .catch(err=>{
        
    })
}

const openModal = (accident)=>{
    modalOpen.value = true
    openedAccident.value = accident
}
</script>
<template>
    <div>
        <h1 class="text-2xl mb-3 ml-3">All Fire Accidents</h1>
        <div class="overflow-hidden sm:rounded-lg">
            <div class="flex flex-wrap">
                <div v-for="accident in accidents" :class="[accident.broadcasted? 'alerts-border' : '']" class="bg-white flex p-2 md:w-5/12 m-3 justify-between items-center space-x-3 rounded-md">
                    <div class="flex flex-col">
                        <p>Accident Code: {{accident.id}}</p>
                        <p>Latitude: {{accident.latitude}}</p>
                        <p>Longitude: {{accident.longitude}}</p>
                        <p>Occured: {{accident.created_time}}</p>
                        <div class="flex space-x-2">
                            <button @click="complete(accident.id)" class="flex items-center text-white bg-green-600 px-3 py-1 rounded-sm cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-all text-white" viewBox="0 0 16 16">
                                    <path d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992a.252.252 0 0 1 .02-.022zm-.92 5.14.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486-.943 1.179z"/>
                                </svg>
                                <span>Complete</span>
                            </button>
                            <button @click="remove(accident.id)" class="flex items-center text-white bg-red-600 px-3 py-1 rounded-sm cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/>
                                </svg>
                                <span>Remove</span>
                            </button>
                        </div>
                    </div>
                    <div class="group relative w-1/2 h-36 overflow-hidden">
                        <button title="zoom" @click="openModal(accident)" class="hidden group-hover:inline absolute top-0 right-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" fill="currentColor" class="bi bi-eye-fill text-red-500" viewBox="0 0 16 16">
                              <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                              <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                            </svg>
                        </button>
                        <iframe 
                        width="300" 
                        height="170" 
                        frameborder="0" 
                        scrolling="no" 
                        marginheight="0" 
                        marginwidth="0" 
                        :src="'https://maps.google.com/maps?q='+accident.latitude+','+accident.longitude+'&hl=es&z=12&amp;output=embed'"
                        >
                        </iframe>
                    </div>  
                </div>        
            </div>    
        </div>        
    </div>
    <div v-if="modalOpen" class="fixed z-20 inset-5 bg-white p-2 flex flex-col justify-center items-center rounded-xl">
        <button @click="modalOpen=false" class="absolute -top-2 -right-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-2xl bg-red-500 text-white rounded-full">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
        <iframe 
        width="300" 
        height="170" 
        frameborder="0" 
        scrolling="no" 
        marginheight="0" 
        marginwidth="0" 
        class="w-full h-full"
        :src="'https://maps.google.com/maps?q='+openedAccident.latitude+','+openedAccident.longitude+'&hl=es&z=17&amp;output=embed'"
        >
        </iframe>
    </div>
    <div v-if="modalOpen" @click="modalOpen=false" class="absolute -inset-full opacity-50 bg-black z-10"></div>
</template>
