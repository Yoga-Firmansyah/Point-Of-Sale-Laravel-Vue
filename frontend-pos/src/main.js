
import { createApp } from 'vue'
import App from './App.vue'
import { createPinia } from 'pinia'
import router from './routes'
import VueNumberFormat from '@coders-tm/vue-number-format'
import VueBarcode from '@chenfengyuan/vue-barcode';
import dayjs from 'dayjs';
import "dayjs/locale/id";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { createPersistedState } from 'pinia-plugin-persistedstate'
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

const app = createApp(App)
const pinia = createPinia();

pinia.use(createPersistedState({
    storage: sessionStorage,
}));

app.use(pinia);
app.use(router)
app.use(VueNumberFormat)
app.component(VueBarcode.name, VueBarcode);
app.component('VueDatePicker', VueDatePicker);
app.component('EasyDataTable', Vue3EasyDataTable);

app.mixin({

    methods: {

        moneyFormat(number) {
            const isNegative = number < 0;
            let absoluteNumber = Math.abs(number);
            let reverse = absoluteNumber.toString().split('').reverse().join(''),
                thousands = reverse.match(/\d{1,3}/g);
            thousands = thousands.join('.').split('').reverse().join('');
            return isNegative ? '-' + thousands : thousands;
        },

        formatDate(dateString) {
            const date = dayjs(dateString).locale('en')
            return date.format('dddd, MMMM D, YYYY');
        },

        addZero(value) {
            return value.toString().padStart(10, '0')
        }


    }
})
app.mount('#app')
