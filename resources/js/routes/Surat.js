export default {
    path:'/:surat/:jenis',
    component:() => import('../views/index.vue'),
    meta:{auth:true},
    children:[
        {
            path:'',
            name:'surat.index',
            component : () => import('../views/surat/index.vue')
        },
        {
            path:'create',
            name:'surat.create',
            component : () => import('../views/surat/create.vue')
        },
        {
            path:':id/edit',
            name:'surat.edit',
            component: () => import('../views/surat/edit.vue')
        }

    ]
}

