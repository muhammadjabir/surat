export default {
    namespaced:true,
    state : {
        dialogData : false,
    },
    mutations : {
        Dialog : (state , payload) => {
            state.dialogData = payload
        },

    },

    actions : {

        setDialog : ({commit},payload) =>{
            commit('Dialog',payload)
        },
    },

    getters : {
        dialogData : state => state.dialogData,
    }
}
