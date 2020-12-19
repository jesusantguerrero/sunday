<template>
  <data-table :cols="cols" :tableData="tableData">
    <template v-slot:concept="{ scope }">
      <input name="" v-model="scope.row.concept" class="form-control" />
    </template>

    <template v-slot:account="{ scope }">
      <resource-selector
        :resource="scope.row.account"
        resource-url="/accounts"
        v-model="scope.row.account_id"
        resource-id="id"
        label="name"
      />
    </template>

    <template v-slot:debit="{ scope }">
      <input
        name=""
        type="number"
        v-model="scope.row.debit"
        class="form-control"
        @input="cols[2].onInput(scope.row, scope)"
      />
    </template>

    <template v-slot:credit="{ scope }">
      <input
        name=""
        type="number"
        v-model="scope.row.credit"
        class="form-control"
        @input="cols[3].onInput(scope.row)"
      />
    </template>

    <template v-slot:actions="{ scope }">
      <button
        @click="removeRow(scope.$index, scope.row)"
        class="invoice-grid__remove-row"
      >
        <font-awesome-icon icon="trash-alt" />
      </button>
    </template>

    <!-- table slots -->
    <template v-slot:append>
      <button
        @click="addMode = true"
        class="invoice-grid__add-row"
        v-if="!addMode"
      >
        Add Row
      </button>
      <div v-else="" class="service-select">
        <multiselect
          v-model="rowToAdd"
          name="invoice-service"
          id="invoice-service"
          label="name"
          track-by="id"
          placeholder="Type a service"
          open-direction="bottom"
          :options="services"
          :searchable="true"
          :loading="isLoading"
          :internal-search="false"
          :options-limit="300"
          :limit="3"
          :max-height="600"
          :show-no-results="false"
          :show-labels="false"
          @search-change="searchServices"
          @input="addRow"
        >
        </multiselect>

        <button @click="addMode = false" class="invoice-grid__remove-row">
          <font-awesome-icon icon="trash-alt" />
        </button>
      </div>
    </template>
  </data-table>
</template>

<script>
import DataTable from "../tables/DataTable";
import ResourceSelector from "@/components/insane/ResourceSelector";
import cols from "./cols";

export default {
  components: {
    DataTable,
    ResourceSelector
  },
  props: {
    tableData: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      cols,
      services: [],
      isLoading: false,
      rowToAdd: {},
      addMode: false
    };
  },

  methods: {
    addRow() {
      this.tableData.push({
        service_name: this.rowToAdd.name,
        concept: this.rowToAdd.name,
        service_id: this.rowToAdd.id,
        quantity: 1,
        discount: 0,
        price: this.rowToAdd.price,
        amount: 0
      });

      this.rowToAdd = {};
      this.addMode = false;
    },

    removeRow(index, row) {
      const isConfirmed = confirm("Do you want to delete this?");
      if (!isConfirmed) {
        return;
      }
      const deleted = { ...row };
      this.tableData.splice(index, 1);
      this.$emit("deleted", deleted);
    }
  }
};
</script>

<style lang="scss">
.el-table,
.el-table__body-wrapper {
  overflow: visible;

  .form-control {
    border: none;
    background: transparent;

    &:focus {
      outline: none;
      box-shadow: none;
    }
  }
}
</style>

<style lang="scss" scoped>
.invoice-grid {
  &__add-row {
    width: 100%;
    height: 34px;
    color: dodgerblue;
    background: white;
    border: none;
    font-weight: bolder;
    transition: all ease 0.3s;

    &:hover {
      font-size: 1.1em;
    }

    &:focus {
      outline: none;
    }
  }

  &__remove-row {
    background: transparent;
    color: red;
    border: none;
  }
}

.service-select {
  display: grid;
  grid-template-columns: 95% 5%;
  padding: 12px 10px;
  overflow: visible;
}
</style>
