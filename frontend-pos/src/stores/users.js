import { ref } from 'vue';
import { defineStore } from 'pinia';
import axios from '../plugins/axios';

export const useUsersStore = defineStore('users', () => {
    const users = ref([]);

    async function getUsers() {
        await axios.get('/users')
            .then(response => {
                users.value = response.users
            })
    }

    async function deleteUser(userId) {
        await axios.delete(`/users/${userId}`)
            .then(() => {
                axios.get('/users')
                    .then(response => {
                        users.value = response.users
                    })
            })
    }

    return { users, getUsers, deleteUser };
}
);
