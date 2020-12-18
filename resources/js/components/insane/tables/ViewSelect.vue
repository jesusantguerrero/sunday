<template>
  <el-popover placement="bottom" width="250" v-model="show">
    <div>Views for {{ section }}</div>
    <ul class="view-select">
      <li
        class="view-select__item"
        v-for="view in views"
        :key="view.name"
        @click="setView(view)"
      >
        {{ view.label }}
      </li>
      <li class="view-select__item" divided>
        <el-popover placement="bottom" width="250" trigger="click" v-if="section">
          <input
            type="text"
            class="form-control"
            :placeholder="selectedType.label"
            v-model="viewName"
          />
          <ul class="view-select">
            <li
              class="view-select__item"
              :class="{ selected: selectedType.id == view.id }"
              v-for="view in viewTypes"
              :key="view.name"
              @click="setSelectedView(view)"
            >
              {{ view.label }}
            </li>
            <li>
              <el-button @click="createView()">
                Crear Vista
              </el-button>
            </li>
          </ul>
          <span slot="reference" class="w-100">
            New View
          </span>
        </el-popover>
      </li>
    </ul>
    <el-button slot="reference">
      {{ selectedView.label }} <i class="el-icon-arrow-down el-icon--right"></i>
    </el-button>
  </el-popover>
</template>

<script>
export default {
  props: {
    views: {
      type: Array,
      required: true
    },
    viewTypes: {
      type: Array,
      required: true
    },
    section: {
      type: String,
      default: ""
    },
    value: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      show: false,
      selectedView: {},
      selectedType: {},
      viewName: ""
    };
  },
  created() {
    this.setView(this.views[0]);
    this.setSelectedView(this.viewTypes[0]);
  },
  methods: {
    setView(view) {
      if (this.selectedView.name !== view.name) {
        this.selectedView = view;
        this.$emit("input", view);
        this.show = false;
      }
    },
    setSelectedView(view) {
      if (this.selectedType.name !== view.name) {
        this.selectedType = view;
      }
    },

    createView() {
      this.$http({
        url: "/ic-views",
        method: "post",
        data: {
          name: this.viewName || this.selectedType.name,
          label: this.viewName || this.selectedType.name,
          view_type_id: this.selectedType.id,
          section: this.section
        }
      }).then(() => {
        this.viewName = "";
      });
    }
  }
};
</script>

<style lang="scss">
.view-select {
  list-style: none;
  margin: 10px 2px;
}

.view-select__item {
  padding: 2px 15px;
  font-size: 16px;
  cursor: pointer;

  &:hover,
  &.selected {
    background: #eee;
  }
}
</style>
