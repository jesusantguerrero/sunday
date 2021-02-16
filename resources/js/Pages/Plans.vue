<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div>
            <div class="max-w-8xl mx-auto py-10 sm:px-6 lg:px-8">
                <!-- Plans -->
                <div class="plans__container mt-5">
                    <h4 class="font-bold mx-2 text-3xl mb-2">Plans</h4>
                    <div class="flex justify-between">
                        <data-billing-card
                            v-for="plan in plans"
                            :key="plan.id"
                            :plan="plan"
                            :subscribe-link="`/subscriptions/${plan.paypal_plan_id}/subscribe`"
                            :subscribe-label="getLabelSubscribe(plan)"
                        >
                        </data-billing-card>
                    </div>
                </div>
                <!-- /Plans -->
                   <div id="paypal-button-container"></div>

                <jet-section-border />
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import DataCard from "../components/DataCard.vue";
import DataPlanCard from "../components/DataPlanCard.vue";
import DataBillingCard from "../components/DataBillingCard.vue";
import { format } from 'date-fns';

export default {
    props: {
        plans: {
            type: Array,
            default() {
                return []
            }
        },
        subscriptions: {
            type: Array,
            default() {
                return []
            }
        }
    },
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

        isBigger(plan) {
            return (
                this.visibleSubscriptions.length &&
                this.visibleSubscriptions[0].quantity < plan.quantity
            );
        }
    }
};
</script>
