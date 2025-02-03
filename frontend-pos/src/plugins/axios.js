import axios from "axios";
import router from "../routes"
import { useAuthStore } from '../stores/auth';
import { storeToRefs } from "pinia";


const request = axios.create({
    baseURL: 'http://localhost:8000/api',
})


request.interceptors.request.use((config) => {
    const store = useAuthStore()
    const { token } = storeToRefs(store);
    let tokenAuth = `Bearer ${token.value}`
    config.headers["Authorization"] = tokenAuth
    return config
})

request.interceptors.response.use(response => {
    console.log("sukses dari plugins", response.data);
    return Promise.resolve(response.data);
}, async error => {
    console.log("error dari plugins", error);
    if (error.response && error.response.status === 401) {
        const store = useAuthStore()
        store.logout();
        await router.push("/auth/login");
    } else {
        return Promise.reject(error.response);
    }
})


export default request