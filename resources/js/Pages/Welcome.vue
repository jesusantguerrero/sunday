<template>
    <div>
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">

        </div>
    </div>
</template>

<script>
    import JetApplicationLogo from './../Jetstream/ApplicationLogo'

    export default {
        components: {
            JetApplicationLogo,
        },
        props: {
            items: {
                type: Array,
                default() {
                    return []
                }
            }
        },
        data() {
            return {
                boardName: ''
            }
        },
        methods: {
            addBoard() {
                if (this.boardName.trim()) {
                    axios({
                        url:'api/boards',
                        method: 'post',
                        data: {
                            name: this.boardName
                        }
                    }).then(() => {
                        this.$inertia.reload()
                    })
                }
            },

            deleteBoard(id) {
                axios({
                    url:`api/boards/${id}`,
                    method: 'delete',
                }).then(() => {
                    this.$inertia.reload()
                })
            }
        }
    }
</script>
