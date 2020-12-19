<template>
  <div class="invoice-totals">
    <p class="total-labels">
      <span class="label">Subtotal: </span>
      <span class="value">{{ totals.subtotal }}</span>
    </p>

    <p class="total-labels">
      <span class="label">Discount:</span>
      <span class="value">{{ totals.discountTotal }}</span>
    </p>

    <p class="total-labels">
      <span class="label"> Total: </span>
      <span class="value">{{ totals.total }}</span>
    </p>

    <div v-if="payments && payments.length">
      <a
        class="total-labels"
        href="#"
        v-for="payment in payments"
        :key="payment.id"
        @click.prevent="$emit('edit-payment', payment)"
      >
        <span class="label"> {{ payment.concept }} </span>
        <span class="value">- {{ payment.amount }}</span>
      </a>
    </div>

    <p class="total-labels">
      <span class="label"> Debt: </span>
      <span class="value">{{ debt }}</span>
    </p>
    <slot name="add-payment" v-if="debt"> </slot>
  </div>
</template>

<script>
export default {
  props: {
    tableData: {
      type: Array,
      required: true
    },
    subtotalField: {
      type: String,
      required: true
    },
    discountField: {
      type: String,
      required: true
    },
    payments: {
      type: Array
    },
    totalField: {
      type: String,
      required: true
    },
    totalValues: {
      type: Object,
      required: true
    }
  },
  computed: {
    totals() {
      return this.tableData.reduce(
        (total, row) => {
          total.total += Number(row[this.totalField]);
          total.discountTotal += Number(row[this.discountField]);
          total.subtotal += Number(row[this.subtotalField]);
          return total;
        },
        {
          total: 0,
          discountTotal: 0,
          subtotal: 0
        }
      );
    },

    paymentTotal() {
      if (this.payments && this.payments.length) {
        return this.payments.reduce((total, payment) => {
          return (total += payment.amount);
        }, 0);
      }
      return 0;
    },

    debt() {
      return this.totals.total - this.paymentTotal;
    }
  },

  watch: {
    totals: {
      handler() {
        if (this.totals && Object.keys(this.totals).length) {
          Object.keys(this.totals).forEach(key => {
            this.$set(this.totalValues, key, this.totals[key]);
          });
        }
      },
      deep: true,
      immediate: true
    }
  }
};
</script>

<style lang="scss">
.total-labels {
  color: #909399;
  font-weight: 700;
  margin-bottom: 0.5rem;
  display: grid;
  grid-template-columns: 1fr 100px;
  grid-column-gap: 5px;
  padding-right: 100px;

  .value {
    text-align: right;
    color: #666;
    font-weight: bold;
  }

  .label {
    text-align: right;
  }
}

.invoice-totals {
  margin-top: 1rem;
}
</style>
