import { createRouter, createWebHistory } from "vue-router";
import auth from "./authRouter";
import dashboard from "./dashboard"
import users from "./users";
import admin from "./adminRouter";
import error from "./errorRouter";
import { useAuthStore } from '../stores/auth'
import { storeToRefs } from "pinia";

const routes = [...auth, ...dashboard, ...users, ...admin, ...error]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title + " - Point Of Sales";

    const userAuth = useAuthStore();
    const { token } = storeToRefs(userAuth);

    if (to.meta.canntoAccessAfterLogin) {
        if (userAuth.token) {
            next('/')
        }
    }

    if (to.meta.requiresAuth) {
        if (!token.value) {
            return next({ name: 'login', query: { redirect: to.fullPath } });
        }

    }

    if (to.meta.requiresRoleAdmin) {
        if (userAuth.role != 'Admin') {
            next('/404')
        }
    }

    if (to.meta.requiresRoleCashier) {
        if (userAuth.role != 'Cashier') {
            next('/404')
        }
    }

    if (!to.matched.length) {
        next('/404');
    } else {
        next();
    }
})
export default router