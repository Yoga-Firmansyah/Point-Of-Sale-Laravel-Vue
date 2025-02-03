import { ref } from 'vue'
import { defineStore } from 'pinia'
import axios from '../plugins/axios'

export const usePurchasesStore = defineStore('purchase', () => {

    const purchases = ref([]);
    const currentMonth = new Date().getMonth() + 1;
    const currentYear = new Date().getFullYear();
    const filteredPurchaseData = ref([]);

    async function getPurchases() {
        await axios.get('/purchases')
            .then(response => {
                purchases.value = response.purchases
            })
    }

    async function deleteData(id) {
        await axios.delete(`/purchases/${id}`)
            .then(() => {
                axios.get('/purchases')
                    .then(response => {
                        purchases.value = response.purchases
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
        filteredPurchaseData.value = purchases.value.filter((purchase) => {
            return new Date(purchase.date).toISOString().split('T')[0] == new Date().toISOString().split('T')[0]
        })
    }
    function month() {
        filteredPurchaseData.value = purchases.value.filter((purchase) => {
            const purchaseMonth = new Date(purchase.date).getMonth() + 1;
            const purchaseYear = new Date(purchase.date).getFullYear();
            return purchaseMonth == currentMonth && purchaseYear == currentYear
        })
    }
    function year() {
        filteredPurchaseData.value = purchases.value.filter((purchase) => {
            const purchaseYear = new Date(purchase.date).getFullYear();
            return purchaseYear == currentYear
        })
    }

    function custom(startDate, endDate) {
        filteredPurchaseData.value = purchases.value.filter((purchase) => new Date(purchase.date).toISOString().split('T')[0] >= new Date(startDate).toISOString().split('T')[0] && new Date(purchase.date).toISOString().split('T')[0] <= new Date(endDate).toISOString().split('T')[0])
    }

    return { purchases, filteredPurchaseData, getPurchases, deleteData, day, month, year, custom }
},
    {
        persist: true,
    },
)