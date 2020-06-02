export default {
    path:'/jenis-surat',
    component:() => import('../views/index.vue'),
    meta:{auth:true},
    children:[
        {
            path:'',
            name:'jenis-surat.index',
            component : () => import('../views/jenis-surat/index.vue')
        },
        {
            path:'create',
            name:'jenis-surat.create',
            component : () => import('../views/jenis-surat/create.vue')
        },
        {
            path:':id/edit',
            name:'jenis-surat.edit',
            component: () => import('../views/jenis-surat/edit.vue')
        }

    ]
}

