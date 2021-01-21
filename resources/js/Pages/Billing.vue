<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div>
            <div class="max-w-8xl mx-auto py-10 sm:px-6 lg:px-8">

                <!-- Plan Statistics -->
                <div class="plans__info flex mb-10">
                    <div v-for="info in cards" :key="info.title" class="w-4/12 mx-2 p-5 bg-white">
                         <h5>
                            {{ info.title }}
                         </h5>
                         <div class="remark_info">
                             {{ info.value }}

                             </div>
                         </div>

                         <div class="links_container">
                             <a :href="link.ref" v-for="link in links" :key="link.ref">
                                 {{ link.label }}
                             </a>
                         </div>
                    </div>
                <!-- /plan Statistics -->

                <!-- Current Plan -->
                <div class="subscriptions__container">
                    <h4 class="font-bold mx-2"> Current Plan</h4>
                    <div
                        class="bg-white px-5 py-10 mb-5 mx-2 rounded-md"
                        v-for="plan in visibleSubscriptions" :key="plan.id">

                        <div class="">
                            <div class="prose prose-xl mx-auto">
                                <h4 class="text-center"> {{ plan.name }}</h4>
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="prose bg-cool-gray-600 text-white px-5 py-2 my-2 rounded-md mx-auto">
                            </div>
                        </div>

                        <div class="text-center">
                            <button
                                class="bg-cool-gray-500 text-white px-5 py-2 inline-block rounded-md"
                                v-if="plan.status== 'Active'"
                                @click="sendPost(`/subscriptions/${plan.id}/agreement/${plan.agreement_id}/suspend`)"
                                >
                                  Suspend
                            </button>
                            <button
                                class="bg-green-500 text-white px-5 py-2 inline-block rounded-md"
                                v-if="plan.status== 'Suspended'"
                                @click="sendPost(`/subscriptions/${plan.id}/agreement/${plan.agreement_id}/reactivate`)">
                                   Reactivate
                            </button>
                            <button
                                class="bg-red-500 text-white px-5 py-2 inline-block rounded-md"
                                v-if="['Active', 'Suspended'].includes(plan.status)"
                                @click="sendPost(`/subscriptions/${plan.id}/agreement/${plan.agreement_id}/cancel`)">
                                   cancel
                            </button>
                        </div>

                    </div>
                </div>
                <!-- /Current Plan -->

                <!-- Plans -->
                <div class="plans__container mt-5">
                    <h4 class="mx-2"> Plans </h4>
                    <div class="flex">
                        <div
                            class="bg-white px-5 py-10 mb-5 mx-2 rounded-md"
                            v-for="plan in plans" :key="plan.id">
                            <div class="prose prose-xl">
                                <h4 class="text-center "> {{ plan.name }}
                                    <span
                                    v-if="isCurrentPlan(plan)"
                                    class="rounded-md text-purple-600 px-1 py-1 text-xs"> Current Plan</span>
                                </h4>
                            </div>

                            <div class="prose bg-cool-gray-600 text-white px-5 py-2 my-2 rounded-md">
                                {{ plan }}
                            </div>

                            <div class="text-center" v-if="!isCurrentPlan(plan)">
                                <a
                                    class="border-2 border-blue-500 bg-white text-blue-500 px-5 py-2 inline-block rounded-md"
                                    :href="`/subscriptions/${plan.paypal_plan_id}/subscribe`">
                                    Contact Sales
                                </a>

                                <a
                                    class="bg-blue-500 text-white px-5 py-2 inline-block rounded-md"
                                    :href="`/subscriptions/${plan.paypal_plan_id}/subscribe`">
                                    <span class="font-extrabold">
                                        Paypal
                                    </span>
                                    {{ visibleSubscriptions.length ? getLabelSubscribe(plan) : 'Subscribe' }}
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /Plans -->

                <jet-section-border />
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetSectionBorder from '@/Jetstream/SectionBorder'

    export default {
        props: ['sessions', 'plans', 'subscriptions'],
        components: {
            AppLayout,
            JetSectionBorder
        },
        computed: {
            visibleSubscriptions() {
                return this.subscriptions.filter(subs => subs.status != 'Cancelled')
            },

            cards() {
                return [
                    {
                        title: "Current Monthly Bill",
                        value: this.pendingBalance,
                        links: [

                        ]
                    },
                    {
                       title: "Next Payment Due",
                        value: this.nextPaymentDate,
                        links: [

                        ]
                    },
                    {
                        title: "Payment Information",
                        value: this.lastPaymentDate,
                        links: [

                        ]
                    },
                    {
                        title: "Last Payment",
                        value: this.lastPayment,
                        links: [

                        ]
                    }
                ]
            },

            details() {
                return this.visibleSubscriptions.length && this.visibleSubscriptions[0].agreements.agreement_details
            },

            pendingBalance() {
                if (this.details) {
                    return `${this.details.outstanding_balance.currency} ${this.details.outstanding_balance.value}`
                }
                return 0;
            },

            lastPayment() {
                if (this.details) {
                    return `${this.details.outstanding_balance.currency} ${this.details.outstanding_balance.value}`
                }
                return "-"
            },

            nextPaymentDate() {
                if (this.details) {
                    return this.details.next_billing_date
                }
                return 0;
            },

            lastPaymentDate() {
                if (this.details) {
                    return this.details.last_payment_date
                }
                return "-"
            }
        },
        methods: {
            sendPost(url) {
                axios.post(url).then(() => {
                    this.$inertia.reload();
                })
            },

            isCurrentPlan(plan) {
                console.log(this.visibleSubscriptions[0].quantity, plan.quantity)
                return this.visibleSubscriptions.length && this.visibleSubscriptions[0].quantity == plan.quantity;
            },

            getLabelSubscribe(plan) {
                return this.isBigger(plan) ? "Upgrade" : "Downgrade";
            },

            isBigger(plan) {
                return this.visibleSubscriptions.length && this.visibleSubscriptions[0].quantity < plan.quantity
            }
        }
    }
</script>
