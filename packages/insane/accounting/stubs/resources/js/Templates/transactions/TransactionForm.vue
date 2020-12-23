<template>
  <section class="section">
    <div class="row invoice-actions section-header d-flex">
      <div class="col-md-6 d-flex">
        <inertia-link :href="`${section}s`" class="btn btn-danger">
          Back
        </inertia-link>
        <h2 class="invoice-title">
          {{ invoice.concept }} #{{ invoice.number }}
          <small class="badge badge-primary">
            {{ invoice.status || "draft" }}
          </small>
        </h2>
      </div>
      <div class="col-md-2"></div>
      <div class="col-md-2"></div>
      <div class="col-md-2">
        <div class="btn-group w-100">
          <button class="btn btn-primary" @click="saveInvoice(2)">
            Save
          </button>
          <button
            class="btn btn-primary dropdown-toggle dropdown-toggle-split"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            v-if="invoice.id"
          >
            <span class="sr-only"> toggle dropdown</span>
          </button>
          <div class="dropdown-menu dropdown-menu-right">
            <button
              type="button"
              class="dropdown-item"
              @click="cloneInvoice()"
              v-if="invoice.id"
            >
              Clone
            </button>
            <button
              type="button"
              class="dropdown-item"
              @click="saveInvoice()"
              v-if="isDraft"
            >
              Draft
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="section-body">
      <div class="row invoice-body">
        <el-collapse v-model="activeSections" class="col-md-12">
          <el-collapse-item title="Logo, concept and description" name="header">
            <div class="invoice-header-details" v-if="invoice.id">
              <div class="image-container"></div>

              <div class="invoice-details">
                <div class="invoice-form-row form-group">
                  <label for="invoice-description">Description: </label>
                  <input
                    type="text"
                    class="form-control"
                    name="invoice-description"
                    id="invoice-description"
                    v-model="invoice.description"
                  />
                </div>
                <div class="invoice-form-row form-group">
                  <label for="invoice-description">Concept: </label>
                  <input
                    type="text"
                    class="form-control"
                    name="invoice-description"
                    id="invoice-description"
                    v-model="invoice.concept"
                  />
                </div>
              </div>
            </div>
          </el-collapse-item>
        </el-collapse>

        <div class="col-md-6 text-left">
          <div class="invoice-form-row form-group">
            <label for="invlice-client">Client: </label>
            <multiselect
              v-model="invoice.client"
              name="invoice-client"
              id="invoice-client"
              label="names"
              track-by="id"
              placeholder="Escriba el nombre del cliente"
              open-direction="bottom"
              :options="clients"
              :searchable="true"
              :loading="loadingClients"
              :internal-search="false"
              :options-limit="300"
              :limit="3"
              :max-height="600"
              :show-no-results="false"
              :show-labels="false"
              @search-change="getClients"
            >
            </multiselect>
          </div>
        </div>

        <div class="col-md-4 offset-md-2 text-left">
          <div class="form-group invoice-form-row">
            <label for="invoice-date">Date</label>
            <el-date-picker
              v-model="invoice.date"
              id="invoice-date"
              type="date"
              title="seleccione una fecha"
              placeholder="selecciona una fecha"
            />
          </div>
        </div>
      </div>

      <transaction-grid :tableData="tableData" />

      <div class="totals-container">
        <transaction-totals
          :tableData="tableData"
          :subtotal-field="totals.subtotalField"
          :discount-field="totals.discountField"
          :payments="invoice.paymentDocs"
          :total-values="totalValues"
          :total-field="totals.totalField"
          :subtotalFormula="totals.subtotalFormula"
          :discountFormula="totals.discountFormula"
          :totalFormula="totals.totalFormula"
          @edit-payment="editPayment"
        >
          <template v-slot:add-payment v-if="!isDraft">
            <button
              class="invoice-totals__add-payment"
              @click="isPaymentDialogVisible.value = true"
            >
              Add Payment
            </button>
          </template>
        </transaction-totals>
      </div>

      <!-- footer and notes -->
      <el-collapse v-model="activeSections" class="col-md-12">
        <el-collapse-item title="Footer and notes" name="footer">
          <div class="invoice-footer-details" v-if="invoice.id">
            <div class="setDatae-container">
              <textarea
                name=""
                class="form-control"
                id=""
                cols="30"
                rows="5"
                v-model="invoice.footer"
              ></textarea>
            </div>

            <div class="invoice-details">
              <textarea
                name=""
                class="form-control"
                id=""
                cols="30"
                rows="5"
                v-model="invoice.notes"
              ></textarea>
            </div>
          </div>
        </el-collapse-item>
      </el-collapse>
      <!-- end of footer and notes -->
    </div>
  </section>
</template>

<script>
import { format as formatDate } from "date-fns";
import TransactionTotals from "@/components/insane/transaction/TransactionTotals";
import TransactionGrid from "@/components/insane/transaction/TransactionGrid";

const defaultTransaction = {
  concept: "Invoice",
  lineItems: []
};

export default {
  components: {
    TransactionGrid,
    TransactionTotals
  },
  props: {
    type: {
      type: String,
      default: "INVOICE"
    },
    data: {
        type: Object,
        required: true
    }
  },

  data() {
    return {
      totalValues: {},
      totals: {
        subtotalField: "subtotal",
        totalField: "amount",
        discountField: "discountTotal",
        subtotalFormula(row) {
          return row.quantity * row.price;
        },
        totalFormula(row) {
          return row.quantity * row.price;
        },
        discountFormula(row) {
          return row.quantity * row.price;
        }
      },
      invoice: {},
      selectedPayment: null,
      isPaymentDialogVisible: {
        value: false
      },
      activeSections: [],
      tableData: [],
      clients: [],
      loadingClients: false
    };
  },

  filters: {
    formatDate(date) {
      return formatDate(date, "YYYY-MM-DD HH:mm:ss");
    }
  },
  mounted() {
    this.setTransactionData(this.data);
  },
  watch: {
    "$route.path": {
      handler() {
        if (this.$route.params.id) {
          this.getTransaction(this.$route.params.id);
        } else {
          const concept = `${this.type
            .slice(0, 1)
            .toUpperCase()}${this.type.slice(1).toLowerCase()}`;
          this.setTransactionData({ ...defaultTransaction, concept: concept });
        }
      }
    }
  },

  computed: {
    isDraft() {
      return !this.invoice.status || this.invoice.status == "draft";
    },
    section() {
      return this.type.toLowerCase();
    }
  },

  methods: {
    getTransaction(id) {
      id = id || this.invoice.id;
      if (id) {
        this.$http
          .get(
            `/transactions/${id}?relationships=transactionLines,transactionLines.account`
          )
          .then(({ data }) => {
            if (data && data.length) {
              this.setTransactionData(data[0]);
            }
          })
          .catch(err => {
            console.log(err);
          });
      }
    },

    editPayment(payment) {
      this.selectedPayment = payment;
      this.isPaymentDialogVisible.value = true;
    },

    getClients(q) {
      this.loadingClients = true;

      this.$http
        .get(`/clients?filters[names]=%${q}%&filters[surename]=%${q}%`)
        .then(({ data }) => {
          if (data) {
            this.clients = data;
            this.loadingClients = false;
          }
        });
    },

    setTransactionData(data) {
      data.date = formatDate(data.date || new Date(), "YYYY-MM-DD HH:mm:ss");
      this.invoice = data;
      const lines =
        data.transactionLines.sort((a, b) => (a.index > b.index ? 1 : -1)) ||
        [];
      this.tableData = lines.map(line => {
        line.credit = line.type == 1 ? line.amount : 0;
        line.debit = line.type == -1 ? line.amount : 0;
        return line;
      });

      delete this.invoice.transactionLines;
    },

    setRequestData(data) {
      const requestData = {
        ...data,
        items: this.tableData.map((item, index) => {
          item.index = index;
          return item;
        })
      };
      requestData.date = formatDate(data.date, "YYYY-MM-DD");
      requestData.due_date = formatDate(data.due_date, "YYYY-MM-DD");
      requestData.resource_type_id = this.type;
      requestData.concept = requestData.concept || this.section;

      requestData.total = this.totalValues.total;
      requestData.discount = this.totalValues.discountTotal;
      requestData.subtotal = this.totalValues.subtotal;
      requestData.client_id = requestData.client.id;
      delete requestData.lineItems;
      delete requestData.paymentDocs;
      delete requestData.client;

      return requestData;
    },

    saveInvoice(status) {
      const formData = this.setRequestData(this.invoice);
      if (status) {
        formData.status = 2;
      }
      let message = "Invoice created";
      let method = "post";
      let url = `/invoices/`;

      if (this.invoice.id) {
        url = `/invoices/${this.invoice.id}`;
        message = "Invoice updated";
        method = "put";
      }

      this.sendRequest(method, url, formData, message).then(invoice => {
        this.getTransaction(invoice.id);
      });
    },

    cloneInvoice(status) {
      const formData = this.setRequestData(this.invoice);
      if (status) {
        formData.status = 2;
      }

      let message = "Invoice created";
      let method = "post";
      let url = `/invoices/${this.invoice.id}/clone`;

      this.sendRequest(method, url, formData, message)
        .then(invoice => {
          this.getTransaction(invoice.id);
        })
        .catch(err => {
          console.log(err);
        });
    },

    sendRequest(method, url, formData, message) {
      return this.$http[method](url, formData).then(({ data }) => {
        if (data) {
          this.getTransaction(this.invoice.id);
          this.$notify({
            type: "success",
            message: message
          });
        }
        return data;
      });
    },

    formatDate(date) {
      return formatDate(date, "YYYY-MM-DD");
    }
  }
};
</script>

<style lang="scss" scoped>
.totals-container {
  background: white;
  display: flex;
  justify-content: flex-end;
}

.invoice-title {
  padding-left: 15px;
}

.section-body {
  padding: 0 15px;
}

.invoice-actions {
  margin-bottom: 15px;

  .btn {
    height: 38px;
  }

  [class*="col-md"] {
    padding: 0 0 0 0;

    &:first-child {
      padding-left: 15px;
    }

    &:last-child {
      padding-right: 15px;
    }
  }

  .btn,
  button,
  input {
    border-radius: 0 0 0 0 !important;
  }

  .btn-primary {
    background: dodgerblue;
  }
}

.invoice-totals {
  &__add-payment {
    width: 100%;
    height: 34px;
    background: dodgerblue;
    color: white;
    border: none;
    font-weight: bolder;
    transition: all ease 0.3s;

    &:hover {
      font-size: 1.01em;
    }

    &:focus {
      outline: none;
    }
  }
}

.invoice-form-row {
  display: inline-grid;
  width: 100%;
  grid-column-gap: 0;
  grid-template-columns: 20% 80%;

  label {
    display: flex;
    align-items: center;
  }
}

.invoice-header-container {
  position: inherit;
}

.invoice-header-details,
.invoice-footer-details {
  display: grid;
  grid-template-columns: 300px 1fr;
  grid-column-gap: 15px;
  padding: 0 15px;
}

.el-collapse {
  margin-bottom: 15px;
}

section {
  padding-bottom: 25px;
  background: white;
}
</style>
