<template>
    <app-layout :boards="boards">
        <div class="px-8">
            <div class="board__toolbar flex justify-between px-8 pb-24 pt-12">
                <div class="flex text-left">
                    <div class="flex justify-between mr-2">
                        <span class="text-3xl font-bold"> Transanctions </span>
                    </div>
                </div>
            </div>

            <div class="py-12">
                <transaction-list
                    v-if="!transaction"
                    :data="transactions"
                >
                </transaction-list>

                <transaction-form
                    v-else
                    :data="transaction"
                >

                </transaction-form>
            </div>

        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../../../Layouts/AppLayout';
    import TransactionList from '../../../Templates/transactions/TransactionList.vue';
    import TransactionForm from '../../../Templates/transactions/TransactionForm.vue';

    export default {
        name: "Integrations",
        components: {
            AppLayout,
            TransactionList,
            TransactionForm
        },
        props: {
            boards: {
                type: Array,
                default() {
                    return []
                }
            },
            transactions: {
                type: Array,
                default() {
                    return []
                }
            },
            transaction: {
                type: Object
            }

        },
        data() {
            return {
                searchOptions: {}
            }
        }
    }
</script>

<style lang="scss">
.section-card {
    @apply bg-white overflow-hidden shadow-xl mx-2 mb-4;
    &.margin-0 {
        @apply m-0;
    }

    header {
        @apply p-4;
    }

    .body {
        @apply p-4;
        min-height: 5rem;
    }
}

button {
    &:focus {
        outline: 0 !important;
    }
}
</style>
