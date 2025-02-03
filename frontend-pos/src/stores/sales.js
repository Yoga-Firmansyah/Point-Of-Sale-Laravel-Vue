import { ref } from 'vue'
import { defineStore } from 'pinia'
import axios from '../plugins/axios'

export const useSalesStore = defineStore('sale', () => {
    const sales = ref([])
    const currentMonth = new Date().getMonth() + 1;
    const currentYear = new Date().getFullYear();
    const filteredData = ref([]);

    async function getSales() {
        await axios.get('/sales')
            .then(response => {
                sales.value = response.sales
            })
    }

    async function deleteData(id) {
        await axios.delete(`/sales/${id}`)
            .then(() => {
                axios.get('/sales')
                    .then(response => {
                        sales.value = response.sales
                    })

                swal({
                    title: "DELETED!",
                    text: "Your Data Has Been Deleted!",
                    icon: "success",
                    timer: 1000,
                    buttons: false,
                });
            }).catch((err) => {
                console.log("Failed to Delete", err);
                let error;
                if (err.message) {
                    error = err.message;
                } else {
                    error = err.data.message;
                }
                swal({
                    title: "ERROR!",
                    text: error,
                    icon: "error",
                    timer: 5000,
                    buttons: false,
                });
            })
    }

    function day() {
        filteredData.value = sales.value.filter((sale) => {
            return new Date(sale.created_at).toISOString().split('T')[0] == new Date().toISOString().split('T')[0]
        })
    }
    function month() {
        filteredData.value = sales.value.filter((sale) => {
            const saleMonth = new Date(sale.created_at).getMonth() + 1;
            const saleYear = new Date(sale.created_at).getFullYear();
            return saleMonth == currentMonth && saleYear == currentYear
        })
    }
    function year() {
        filteredData.value = sales.value.filter((sale) => {
            const saleYear = new Date(sale.created_at).getFullYear();
            return saleYear == currentYear
        })
    }

    function custom(startDate, endDate) {
        filteredData.value = sales.value.filter((sale) => new Date(sale.created_at).toISOString().split('T')[0] >= new Date(startDate).toISOString().split('T')[0] && new Date(sale.created_at).toISOString().split('T')[0] <= new Date(endDate).toISOString().split('T')[0])
    }

    return { sales, filteredData, getSales, deleteData, day, month, year, custom }
},
    {
        persist: true,
    },
)