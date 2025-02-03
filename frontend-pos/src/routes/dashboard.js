const dashboard = [
    {
        path: '/',
        component: () => import('../layouts/MainLayout.vue'),
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('../views/users/Home.vue'),
                meta: {
                    title: 'Dashboard',
                    requiresAuth: true
                },
            },
            {
                path: 'sales',
                name: 'sales',
                component: () => import('../views/users/sales/Index.vue'),
                meta: {
                    title: 'Sales Page',
                    requiresAuth: true,
                }
            },
            {
                path: 'sales/add',
                name: 'addSale',
                component: () => import('../views/users/sales/Create.vue'),
                meta: {
                    title: 'Sales Page',
                    requiresAuth: true,
                }
            },
            {
                path: 'invoice/:id',
                name: 'invoiceSale',
                component: () => import('../views/users/sales/Invoice.vue'),
                meta: {
                    title: 'Invoice',
                    requiresAuth: true,
                }
            },
            {
                path: '/reports',
                name: 'reports',
                component: () => import('../views/users/reports/Index.vue'),
                meta: {
                    title: 'Reports',
                    requiresAuth: true,
                }
            },
            
        ]
    },
]

export default dashboard