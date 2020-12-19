<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card w-100">
        <div class="card-body section-actions">
          <div class="app-search__container">
            <app-search
              v-model="searchText"
              :placeholder="`search for ${section}s`"
              @dateChanged="dateChanged"
              @filter="filtered"
              @sort="sorted"
              :include-dates="true"
            ></app-search>
          </div>
          <div class="action-buttons">
            <inertia-link
              :href="{ name: `${section}-create` }"
              class="btn btn-primary w-100 d-flex"
            >
              Add {{ section }}
            </inertia-link>
          </div>
        </div>

        <data-table
          :cols="cols"
          :tableData="tableData"
          @cell-click="editInvoice"
        >
          <template v-slot:date="{ scope }">
            <inertia-link
              :href="`${section}s/${scope.row.id}}`"
            >
              {{ scope.row.date | formatDateFilter }}
            </inertia-link>
          </template>

          <template v-slot:status="{ scope }">
            <span :class="`badge badge-${getStatus(scope.row.status)}`">
              {{ scope.row.status }}
            </span>
          </template>

          <template v-slot:concept="{ scope }">
            <span> {{ scope.row.concept }} {{ scope.row.number }} </span>
          </template>

          <template v-slot:actions="{ scope }">
            <el-dropdown
              split-button
              type="primary"
              @command="rowClick($event, scope.row)"
              :data-invoice-id="scope.row.id"
            >
              ...
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="edit" data>Edit</el-dropdown-item>
                <el-dropdown-item command="duplicate"
                  >Duplicate</el-dropdown-item
                >
                <el-dropdown-item command="export">Export</el-dropdown-item>
                <el-dropdown-item command="print">Print</el-dropdown-item>
                <el-dropdown-item command="delete">Delete</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </data-table>
      </div>
    </div>
  </div>
</template>

<script>
import { format as formatDate } from "date-fns";
import AppSearch from "../../components/insane/appSearch";
import DataTable from "../../components/insane/tables/DataTable";
import cols from "./cols";
// import invoiceApi from "@/api/invoice.api";

export default {
  components: {
    DataTable,
    AppSearch
  },

  props: {
    type: {
      type: String,
      default: "INVOICE"
    },
    data: {
        type: Array,
        default() {
            return []
        }
    }
  },

  data() {
    return {
      cols,
      searchText: "",
      tableData: []
    };
  },

  created() {
   this.tableData = this.data;
  },

  filters: {
    formatDateFilter(date) {
        try {
            return formatDate(date, "YYYY-MM-DD") || date;

        } catch {
            return date;
        }
    }
  },

  computed: {
    section() {
      return this.type.toLowerCase();
    }
  },

  watch: {
    "$route.path"() {
      this.getTransactions();
    }
  },

  methods: {
    getTransactions() {
      this.$http.get(`/transactions?sort=-date,-number`).then(({ data }) => {
        if (data) {
          this.tableData = data;
        }
      });
    },

    rowClick(command, invoice) {
      switch (command) {
        case "duplicate":
          invoiceApi
            .clone(invoice.id)
            .then(({ data }) => {
              this.editInvoice(data);
            })
            .catch(err => {
              console.log(err);
            });
          break;
        case "edit":
          this.editInvoice(invoice);
          break;
        case "delete":
          invoiceApi.delete(invoice.id).then(({ data }) => {
            this.tableData = this.tableData.filter(
              invoice => invoice.id != data.id
            );
          });
          break;
        default:
          break;
      }
    },

    dateChanged() {},
    filtered() {},
    sorted() {},

    getStatus(status) {
      const statusColors = {
        draft: "primary",
        unpaid: "success",
        partial: "success"
      };

      return !statusColors[status] ? "primary" : statusColors[status];
    },

    editInvoice(invoice, cell) {
      if (cell && cell.property == "actions") {
        return;
      }

      this.$inertia.visit(`transactions/${invoice.id}`);
    },

    formatDate(date) {
      return formatDate(date, "YYYY-MM-DD HH:mm:ss");
    }
  }
};
</script>

<style lang="scss" scoped>
.totals-container {
  background: white;
  display: flex;
}

.section-actions {
  display: flex;

  .app-search__container {
    width: 80%;
    margin-right: 15px;
  }

  .action-buttons {
    width: 20%;
    display: flex;

    button {
      margin-left: auto;
    }
  }
}
</style>
