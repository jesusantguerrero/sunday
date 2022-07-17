<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile
            </h2>
        </template>

        <div>
            <div class="py-10 mx-auto max-w-8xl sm:px-6 lg:px-8">
                <!-- Current Plan -->
                <div class="mb-10 subscriptions__container">
                    <h4 class="mx-2 mb-2 text-3xl font-bold">Current Plan</h4>
                    <data-plan-card
                        v-for="plan in visibleSubscriptions"
                        :key="plan.id"
                        :plan="plan"
                        @suspend="sendSubscriptionAction(plan, 'suspend')"
                        @reactivate="sendSubscriptionAction(plan, 'reactivate')"
                        @cancel="sendSubscriptionAction(plan, 'cancel')"
                    >
                    </data-plan-card>
                </div>
                <!-- /Current Plan -->

                <!-- Transactions -->
                <div class="transactions">
                    <h4 class="mx-2 mb-2 text-3xl font-bold">Transactions</h4>
                    <div
                        v-for="transaction in transactions" :key="transaction.id"
                        class="flex justify-around px-5 py-10 mx-2 mb-5 bg-white rounded-md shadow-md"
                        >
                        <div class="w-100">
                            {{ transaction.status }}
                        </div>

                        <div class="w-100">
                            <span>
                                {{ transaction.payer_email }}
                            </span>

                            <span class="ml-2">
                                {{ getPayerName(transaction.payer_name) }}
                            </span>
                        </div>

                        <div class="w-100">
                            <div class="text-purple-500">
                                USD {{ transaction.amount_with_breakdown.net_amount.value }}
                            </div>
                            <div class="text-purple-500">
                                USD {{ transaction.amount_with_breakdown.fee_amount.value }}
                            </div>
                            <div class="font-bold text-green-500">
                                USD {{ transaction.amount_with_breakdown.gross_amount.value }}
                            </div>
                        </div>

                        <div class="w-100">
                            {{ getTransactionDate(transaction.time) }}
                        </div>
                    </div>
                </div>
                <!-- /Transactions -->

                <jet-section-border />
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import DataCard from "../components/DataCard.vue";
import DataPlanCard from "../components/DataPlanCard.vue";
import DataBillingCard from "../components/DataBillingCard.vue";
import { format } from 'date-fns';

export default {
    props: ["sessions", "plans", "subscriptions", "transactions"],
    components: {
        AppLayout,
        JetSectionBorder,
        DataCard,
        DataPlanCard,
        DataBillingCard
    },
    computed: {
        visibleSubscriptions() {
            return this.subscriptions.filter(
                subs => subs.status.toLowerCase() != "cancelled"
            );
        },

        cards() {
            return [
                {
                    title: "Current Monthly Bill",
                    value: this.pendingBalance,
                    links: [
                        {
                            label: "Payment Details",
                            type: "inertia",
                            ref: "/user/billing/current"
                        }
                    ]
                },
                {
                    title: "Next Payment Due",
                    value: this.nextPaymentDate,
                    links: [
                        {
                            label: "View payment history",
                            type: "inertia",
                            ref: "/user/billing/current"
                        }
                    ]
                },
                {
                    title: "Last Payment",
                    value: this.lastPayment,
                    links: []
                },
                {
                    title: "Payment Information",
                    value: this.lastPaymentDate,
                    links: [
                        {
                            label: "Redeem coupon",
                            type: "inertia",
                            ref: "/user/billing/current"
                        }
                    ]
                }
            ];
        },

        details() {
            return (
                this.visibleSubscriptions.length && this.visibleSubscriptions[0]
            );
        },

        pendingBalance() {
            if (this.details) {
                const nextPayment = JSON.parse(this.details.next_payment)
                return nextPayment.currency_code + " " + nextPayment.value;
            }
            return 0;
        },

        lastPayment() {
            if (this.details) {
                const lastPayment = JSON.parse(this.details.last_payment)
                return lastPayment.amount.currency_code + " " + lastPayment.amount.value;
            }
            return "-";
        },

        nextPaymentDate() {
            if (this.details) {
                return format(new Date(this.details.next_billing_date), "MMM dd, yyyy");
            }
            return 0;
        },

        lastPaymentDate() {
            if (this.details) {
                return format(new Date(this.details.last_payment_date), "MMM dd, yyyy");
            }
            return "-";
        }
    },
    methods: {
        sendSubscriptionAction(subscription, actionName) {
            const url = `/v2/subscriptions/${subscription.id}/agreement/${subscription.agreement_id}/${actionName}`;
            axios.post(url).then(() => {
                this.$inertia.reload();
            });
        },

        isCurrentPlan(plan) {
            console.log(this.visibleSubscriptions[0].quantity, plan.quantity);
            return (
                this.visibleSubscriptions.length &&
                this.visibleSubscriptions[0].quantity == plan.quantity
            );
        },

        getLabelSubscribe(plan) {
            return this.isBigger(plan) ? "Upgrade" : "Downgrade";
        },

        getPayerName(payer) {
            return Object.values(payer).join(" ");
        },

        getTransactionDate(date) {
             return format(new Date(date), "MMM dd, yyyy");
        },

        isBigger(plan) {
            return (
                this.visibleSubscriptions.length &&
                this.visibleSubscriptions[0].quantity < plan.quantity
            );
        }
    }
};
</script>
