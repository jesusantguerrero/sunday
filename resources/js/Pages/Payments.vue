<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile
            </h2>
        </template>

        <div>
            <div class="py-10 mx-auto max-w-8xl sm:px-6 lg:px-8">
                <!-- Plan Statistics -->
                <div class="flex mb-10 plans__info">
                    <data-card
                        v-for="info in cards"
                        :key="info.title"
                        :info="info"
                    >
                    </data-card>
                </div>
                <!-- /plan Statistics -->

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

                <!-- Plans -->
                <div class="mt-5 plans__container">
                    <h4 class="mx-2 mb-2 text-3xl font-bold">Plans</h4>
                    <div class="flex justify-between">
                        <data-billing-card
                            v-for="plan in plans"
                            :key="plan.id"
                            :plan="plan"
                            :is-current="isCurrentPlan(plan)"
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
import AppLayout from "@/Layouts/AppLayout.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import DataCard from "../components/DataCard.vue";
import DataPlanCard from "../components/DataPlanCard.vue";
import DataBillingCard from "../components/DataBillingCard.vue";
import { format } from 'date-fns';

export default {
    props: ["sessions", "plans", "subscriptions"],
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
