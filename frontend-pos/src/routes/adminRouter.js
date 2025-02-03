const admin = [
    {
        path: '/admin',
        component: () => import('../layouts/MainLayout.vue'),
        children: [
            {
                path: '',
                redirect: '/'
            },
            {
                path: 'categories',
                name: 'categories',
                component: () => import('../views/users/categories/Index.vue'),
                meta: {
                    title: 'Categories Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'categories/add',
                name: 'addCategory',
                component: () => import('../views/users/categories/Create.vue'),
                meta: {
                    title: 'Categories Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'categories/edit/:id',
                name: 'editCategory',
                component: () => import('../views/users/categories/Edit.vue'),
                meta: {
                    title: 'Categories Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'products',
                name: 'products',
                component: () => import('../views/users/products/Index.vue'),
                meta: {
                    title: 'Products Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'products/add',
                name: 'addProduct',
                component: () => import('../views/users/products/Create.vue'),
                meta: {
                    title: 'Products Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'products/edit/:id',
                name: 'editProduct',
                component: () => import('../views/users/products/Edit.vue'),
                meta: {
                    title: 'Products Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'purchases',
                name: 'purchases',
                component: () => import('../views/users/purchases/Index.vue'),
                meta: {
                    title: 'Purchases Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'purchases/add',
                name: 'addPurchase',
                component: () => import('../views/users/purchases/Create.vue'),
                meta: {
                    title: 'Purchases Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'purchases/edit/:id',
                name: 'editPurchase',
                component: () => import('../views/users/purchases/Edit.vue'),
                meta: {
                    title: 'Purchases Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'users',
                name: 'users',
                component: () => import('../views/users/users/Index.vue'),
                meta: {
                    title: 'User Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'users/add',
                name: 'addUser',
                component: () => import('../views/users/users/Create.vue'),
                meta: {
                    title: 'User Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
            {
                path: 'users/edit/:id',
                name: 'editUser',
                component: () => import('../views/users/users/Edit.vue'),
                meta: {
                    title: 'User Page',
                    requiresAuth: true,
                    requiresRoleAdmin: true,
                }
            },
        ]
    }
]
export default admin;