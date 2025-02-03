import { ref, } from 'vue'
import { defineStore } from 'pinia'
import axios from '../plugins/axios'
import { usePurchasesStore } from './purchases'
import { useReportsStore } from './reports'

export const useAuthStore = defineStore('user', () => {
    const token = ref('')
    const user = ref([])
    const role = ref([])

    if (token.value != '') {
        axios.get('/user')
            .then(response => {
                user.value = response.user
                role.value = response.user.roles[0].name
            })
    }

    function login(user_data, data_token) {
        console.log('USER Is', user_data)
        user.value = user_data
        role.value = user_data.roles[0].name
        token.value = data_token
    }

    async function getAuth() {
        await axios.get('/user')
            .then(response => {
                user.value = response.user
                role.value = response.user.roles[0].name
            })
    }

    async function logout() {
        
        const purchasesStore = usePurchasesStore();
        const reportsStore = useReportsStore();
        
        purchasesStore.purchases = [];
        purchasesStore.filteredPurchaseData = [];
        reportsStore.sales2 = [];
        
        
        token.value = ''
        user.value = []
        role.value = []

        for (let i = 0; i < sessionStorage.length; i++) {
            const key = sessionStorage.key(i);
            sessionStorage.setItem(key, ""); // or set it to null
        }
        
    }

    return { user, token, role, login, logout, getAuth }
},
    {
        persist: true,
    },
)