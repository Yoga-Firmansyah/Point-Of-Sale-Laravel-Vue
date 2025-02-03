import { ref } from 'vue'
import { defineStore } from 'pinia'
import axios from '../plugins/axios'

export const useReportsStore = defineStore('report', () => {
    const sales2 = ref([])
    const currentMonth = new Date().getMonth() + 1;
    const currentYear = new Date().getFullYear();
    const filteredData2 = ref([]);

    async function getSales2() {
        await axios.get('/sales')
            .then(response => {
                sales2.value = response.sales
            })
    }

    function day() {
        filteredData2.value = sales2.value.filter((sale) => {
            return new Date(sale.created_at).toISOString().split('T')[0] == new Date().toISOString().split('T')[0]
        })
    }
    function month() {
        filteredData2.value = sales2.value.filter((sale) => {
            const saleMonth = new Date(sale.created_at).getMonth() + 1;
            const saleYear = new Date(sale.created_at).getFullYear();
            return saleMonth == currentMonth && saleYear == currentYear
        })
    }
    function year() {
        filteredData2.value = sales2.value.filter((sale) => {
            const saleYear = new Date(sale.created_at).getFullYear();
            return saleYear == currentYear
        })
    }

    function custom(startDate, endDate) {
        filteredData2.value = sales2.value.filter((sale) => new Date(sale.created_at).toISOString().split('T')[0] >= new Date(startDate).toISOString().split('T')[0] && new Date(sale.created_at).toISOString().split('T')[0] <= new Date(endDate).toISOString().split('T')[0])
    }

    return { sales2, filteredData2, getSales2, day, month, year, custom }
},
    {
        persist: true,
    },
)