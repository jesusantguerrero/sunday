// type config = {
//     title?: string,
//     content?: string,
//     cancelButtonText?: string,
//     confirmationButtonText?: string,
//     confirm?: Function,
//     cancel?: Function
// }

const config = {
    title: "",
    content: "",
    cancelButtonText: "",
    confirmationButtonText: "",
    confirm: () => {} ,
    cancel: () => {}
}

export default {
    methods: {
        showConfirm(confirmData = config) {
            this.$root.$emit('show-modal', confirmData)
        }
    }
}
