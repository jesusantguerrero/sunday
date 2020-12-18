<template>
  <div>
    <div class="card-body section-actions" v-if="showActionBar">
      <div class="app-search__container">
        <app-search
          v-model.lazy="searchText"
          placeholder="search ..."
          @date-changed="dateChanged"
          @filter="filtered"
          @sort="sorted"
          v-if="showSearch"
          :include-dates="showDates"
          :include-filters="showFilters"
        >
          <template v-slot:date v-if="isTemplate('week')">
            <div class="d-flex">
              <button
                class="btn btn-primary"
                @click="$refs.Schedule.gotoToday()"
              >
                <i class="fa fa-calendar mr-2"> </i>
                Hoy
              </button>
              <span>
                <el-date-picker
                  v-model="selectedDay"
                  format="MMMM dd yyyy"
                  type="date"
                  placeholder="Pick a day"
                />
              </span>
            </div>
          </template>

          <template v-slot:date="scope" v-if="isTemplate('table')">
            <date-range-picker
              v-model="scope.dates"
              :range-mode="true"
              @input="scope.emitDates"
            />
          </template>

          <template v-slot:filter v-if="isTemplate('week')">
            <week-pager-buttons
              @prev-week="$refs.Schedule.prevWeek()"
              @next-week="$refs.Schedule.nextWeek()"
            >
            </week-pager-buttons>
          </template>
        </app-search>
      </div>
      <div class="action-buttons row d-flex justify-content-end">
        <ViewSelect
          v-model="dataTemplate"
          :section="config.item"
          :view-types="viewTypes"
          :views="[
            {
              name: 'table',
              label: 'Table',
              view_type_id: 1
            },
            {
              name: 'week',
              label: 'week',
              view_type_id: 2
            }
          ]"
        />

        <router-link
          :to="{ name: `${section}Create` }"
          class="btn btn-primary ml-2"
          v-if="showAdd"
        >
          Crear {{ config.item }}
        </router-link>

        <el-dropdown
          split-button
          type="primary"
          class="col-auto"
          v-if="showActions"
        >
          Acciones
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item command="edit" data
              >Exportar PDF</el-dropdown-item
            >
            <el-dropdown-item command="delete">Exportar XML</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>

    <div>
      <week-pager
        v-if="isTemplate('week')"
        v-model="week"
        class="p-4 bg-white"
        ref="Schedule"
        :outer-date="selectedDay"
        @date-changed="selectedDay = $event"
      >
        <template v-for="day in week" v-slot:[getISODate(day)]>
          <div :key="day.toISOString()">
            <div
              v-for="(item, index) in getForDate(day)"
              :key="`${item.fecha}-${index}`"
            >
              <show-card :item="item" />
            </div>
          </div>
        </template>
      </week-pager>

      <ic-table
        :is-loading="isLoading"
        :cols="cols"
        :table-data="localData"
        :config="config"
        v-else
        @row-click="$emit('row-click', $event)"
        @sort="serverSort"
      >
        <slot
          v-for="col in cols"
          :slot="col.name"
          slot-scope="props"
          v-bind="{ ...props }"
          :name="col.name"
        >
        </slot>
        <slot
          slot="append"
          slot-scope="props"
          v-bind="{ ...props }"
          name="append"
        >
        </slot>
      </ic-table>
    </div>

    <div
      v-if="config.pagination"
      class="pagination-container d-flex justify-content-end"
    >
      <el-pagination
        background
        @current-change="paginate"
        @size-change="changeSize"
        :current-page.sync="pagination.page"
        layout="total,prev, pager, next,sizes"
        :page-sizes="[10, 20, 50, 100, 200]"
        :page-size.sync="pagination.limit"
        :total="pagination.total"
      >
      </el-pagination>
    </div>
  </div>
</template>

<script>
import debounce from "lodash-es/debounce";
import IcTable from "./IcTable";
import WeekPager from "@/components/insane/week-pager";
import ShowCard from "@/components/insane/week-pager/show-card.vue";
import WeekPagerButtons from "@/components/insane/week-pager/controls.vue";
import ViewSelect from "./ViewSelect";
import DateRangePicker from "./DateRangePicker";

export default {
  name: "data-table",
  components: {
    IcTable,
    WeekPager,
    ShowCard,
    WeekPagerButtons,
    ViewSelect,
    DateRangePicker
  },
  props: {
    cols: {
      type: Array,
      required: true
    },
    section: {
      type: String
    },
    config: {
      type: Object,
      default() {
        return {};
      }
    },
    tableData: {
      type: Array
    }
  },
  data() {
    return {
      searchText: "",
      localData: [],
      selectedDay: new Date(),
      dataTemplate: {},
      mainDateField: "date",
      viewTypes: [],
      week: [],
      sort: {
        order: "",
        column: ""
      },
      dates: {
        startDate: "",
        endDate: ""
      },
      pagination: {
        limit: 10,
        page: 1,
        total: 0,
        lastPage: 0
      },
      isLoading: false
    };
  },
  created() {
    this.cols.forEach((col, index) => {
      this.cols[index].sortable =
        this.cols[index].sortable === true
          ? "custom"
          : this.cols[index].sortable;
    });

    this.getViewTypes();

    if (this.config.resourceUrl) {
      this.getTableData();
    }

    this.getMainDateField();
  },
  watch: {
    tableData: {
      handler(data) {
        this.localData = data;
      },
      immediate: true
    },
    "config.resourceUrl"() {
      this.getTableData();
    },
    searchText: debounce(function() {
      this.getTableData();
    }, 200)
  },
  computed: {
    sortParams() {
      let order = this.sort.order == "descending" ? "-" : "";
      let sort = [];
      order = !this.sort.order ? null : order;

      if (this.config.urlParams && this.config.urlParams.sort) {
        sort.push(...this.config.urlParams.sort);
      }

      if (this.sort.column && order != null) {
        sort.push(`${order}${this.sort.column}`);
      }

      return sort.length ? `sort=${sort.join(",")}` : "";
    },
    searchParams() {
      return this.searchText ? `search=${this.searchText}` : "";
    },
    paginationParams() {
      return this.config.pagination
        ? `limit=${this.pagination.limit}&page=${this.pagination.page}`
        : "";
    },
    relationshipsParams() {
      return this.config.urlParams && this.config.urlParams.relationships
        ? `relationships=${this.config.urlParams.relationships}`
        : "";
    },
    filterParams() {
      let filters = [];
      if (this.config.urlParams && this.config.urlParams.filter) {
        Object.entries(this.config.urlParams.filter).forEach(
          ([name, value]) => {
            filters.push(`filter[${name}]=${value}`);
          }
        );
      }

      if (this.dates.startDate) {
        let dateFilterValue = this.dates.startDate;
        if (this.dates.endDate) {
          dateFilterValue += `~${this.dates.endDate}`;
        }
        filters.push(`filter[${this.mainDateField}]=${dateFilterValue}`);
      }

      return filters.join("&");
    },
    params() {
      let params = [
        this.sortParams,
        this.searchParams,
        this.filterParams,
        this.relationshipsParams,
        this.paginationParams
      ]
        .filter(param => Boolean(param))
        .join("&");
      return params;
    },
    searchbarConfig() {
      if (this.config.searchBar === false) {
        return [];
      } else if (!this.config.searchBar == undefined) {
        return ["search", "dates", "filter", "add", "actions"];
      }
      return this.config.searchBar;
    },
    showAdd() {
      return this.searchbarConfig.includes("add");
    },

    showDates() {
      return this.searchbarConfig.includes("dates");
    },

    showFilters() {
      return this.searchbarConfig.includes("filter");
    },
    showActions() {
      return this.searchbarConfig.includes("actions");
    },
    showSearch() {
      return this.searchbarConfig.includes("search");
    },
    showActionBar() {
      return this.searchbarConfig && this.searchbarConfig.length;
    }
  },
  methods: {
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = "Costo total";
          return;
        }
        const values = data.map(item => Number(item[column.property]));
        if (!values.every(value => isNaN(value))) {
          sums[index] =
            "$ " +
            values.reduce((prev, curr) => {
              const value = Number(curr);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
        } else {
          sums[index] = "N/A";
        }
      });

      return sums;
    },

    getTableData() {
      this.isLoading = true;

      this.$http
        .get(`${this.config.resourceUrl}?${this.params}`)
        .then(({ data }) => {
          if (data) {
            if (Array.isArray(data)) {
              this.localData = data;
            } else {
              this.localData = data.data;
              this.pagination.lastPage = data.lastPage;
              this.pagination.total = data.total;
            }
            this.isLoading = false;
          }
        });
    },

    getViewTypes() {
      this.$http("/ic-view-types").then(({ data }) => {
        this.viewTypes = data.data;
      });
    },

    isTemplate(name) {
      const currentTemplate = this.viewTypes.find(
        type => type.id == this.dataTemplate.view_type_id
      );
      return currentTemplate && currentTemplate.name == name;
    },

    paginate() {
      this.getTableData();
    },

    // eslint-disable-next-line no-unused-vars
    serverSort({ column, order, prop }) {
      this.sort.column = prop;
      this.sort.order = order;
      this.getTableData();
    },

    deleteLocalRow(id) {
      this.localData = this.localData.filter(row => row.id != id);
    },

    getMainDateField() {
      const dateField = this.cols.find(col => col.mainDateField);
      this.mainDateField = dateField ? dateField.name : "date";
    },

    dateChanged(startDate, endDate) {
      this.dates.startDate = startDate;
      this.dates.endDate = endDate;
      this.getTableData();
    },

    filtered() {},

    sorted() {},

    changeSize(size) {
      this.$set(this.pagination, "limit", size);
      this.getTableData();
    },

    // Week Page Mode
    getForDate(date) {
      return this.localData.filter(
        row => row[this.mainDateField] == this.getISODate(date)
      );
    },

    getISODate(date) {
      return date.toISOString().slice(0, 10);
    }
  }
};
</script>

<style lang="scss">
.section-actions {
  display: flex;
  background: white;

  .app-search__container {
    width: 70%;
    margin-right: 15px;
  }

  .action-buttons {
    width: 30%;
    display: flex;

    .btn {
      text-align: center !important;
    }
  }
}
.pagination-container {
  background: white;
  height: 42px;
  width: 100%;
}

.el-table td {
  padding: 4px 0;
}

.el-pagination {
  display: flex;
  align-items: center;

  .btn-next .el-icon,
  .btn-prev .el-icon,
  .el-pager li {
    font-size: 16px;
  }
}

.el-loading-mask {
  z-index: 999;
}
</style>
