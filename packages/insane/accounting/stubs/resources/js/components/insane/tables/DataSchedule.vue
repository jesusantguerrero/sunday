/* eslint-disable no-unused-vars */
<template>
  <div>
    <div class="card-body section-actions" v-if="showActionBar">
      <div class="app-search__container">
        <app-search
          v-model.lazy="searchText"
          placeholder="search ..."
          @dateChanged="dateChanged"
          @filter="filtered"
          @sort="sorted"
          v-if="showSearch"
          :show-button="false"
          :include-dates="true"
          :include-filters="true"
        >
          <template v-slot:date>
            <div class="d-flex">
              <button
                class="btn btn-primary"
                @click="$refs.Schedule.gotoToday()"
              >
                <i class="fa fa-calendar mr-2" />
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

          <template v-slot:filter>
            <week-pager-buttons
              @prev-week="$refs.Schedule.prevWeek()"
              @next-week="$refs.Schedule.nextWeek()"
            >
            </week-pager-buttons>
          </template>
        </app-search>
      </div>
      <div class="action-buttons row d-flex justify-content-end">
        <router-link
          :to="{ name: `${section}Create` }"
          class="btn btn-primary col-auto"
          v-if="showAdd"
        >
          Crear {{ this.config.item }}
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
        v-model="week"
        class="p-4 bg-white"
        ref="Schedule"
        :outer-date="selectedDay"
        @date-changed="selectedDay = $event"
      >
        <template v-for="day in week" v-slot:[getISODate(day)]>
          <div :key="day.toISOString()">
            <div
              v-for="(show, index) in getForDate(day)"
              :key="`${show.date}-${index}`"
            >
              <show-card :show="show" />
            </div>
          </div>
        </template>
      </week-pager>
    </div>
  </div>
</template>

<script>
import debounce from "lodash-es/debounce";
import WeekPager from "@/components/week-pager";
import ShowCard from "@/components/week-pager/show-card.vue";
import WeekPagerButtons from "@/components/week-pager/controls.vue";

export default {
  name: "data-schedule",
  components: {
    WeekPager,
    ShowCard,
    WeekPagerButtons
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
      week: [],
      selectedDay: new Date(),
      sort: {
        order: "",
        column: ""
      },
      filter: [],
      episodes: [
        {
          hour: "08:00 A.M",
          title: "Dragon Ball Z 1",
          episode: "Episode",
          date: "2020-05-10",
          status: 1,
          cover: "http://www.masgamers.com/wp-content/uploads/2020/01/dbzz1.jpg"
        },
        {
          hour: "08:00 A.M",
          title: "Naruto",
          episode: "Episode 1",
          date: "2020-05-10",
          cover: "https://images2.alphacoders.com/101/thumb-1920-1010918.png"
        },
        {
          hour: "08:00 A.M",
          title: "Boruto",
          episode: "Episode 1",
          date: "2020-05-12",
          cover: "https://images2.alphacoders.com/101/thumb-1920-1010918.png"
        },
        {
          hour: "08:00 P.M",
          title: "Saint Saiya",
          episode: "Episode 1",
          date: "2020-05-12",
          status: 2
        }
      ],
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

    if (this.config.resourceUrl) {
      this.getTableData();
    }
  },
  watch: {
    tableData: {
      handler(data) {
        this.localData = data;
      },
      immediate: true
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
      if (this.config.urlParams && this.config.urlParams.filter) {
        let filters = [];
        Object.entries(this.config.urlParams.filter).forEach(
          ([name, value]) => {
            filters.push(`filter[${name}]=${value}`);
          }
        );
        return filters.join("&");
      }
      return "";
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
      return this.searchbarConfig.length;
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

    paginate() {
      this.getTableData();
    },

    getForDate(date) {
      return this.episodes.filter(show => show.date == this.getISODate(date));
    },

    getISODate(date) {
      return date.toISOString().slice(0, 10);
    },

    // eslint-disable-next-line no-unused-vars
    serverSort({ _, order, prop }) {
      this.sort.column = prop;
      this.sort.order = order;
      this.getTableData();
    },

    deleteLocalRow(id) {
      this.localData = this.localData.filter(row => row.id != id);
    },

    dateChanged() {},

    filtered() {},

    sorted() {},

    changeSize(size) {
      this.$set(this.pagination, "limit", size);
      this.getTableData();
    },

    getHeaderClass({ row }) {
      return row.headerClass;
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
      margin-left: auto;
      margin: 0 5px;
      text-align: center !important;
    }
  }
}

.pagination-container {
  background: white;
  height: 42px;
  width: 100%;
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
