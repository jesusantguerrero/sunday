<template>
  <el-table
    :data="tableData"
    v-loading="isLoading"
    stripe
    @sort-change="$emit('sort', $event)"
    style="width: 100%;"
    :header-cell-class-name="getHeaderClass"
    @row-click="$emit('row-click', $event)"
  >
    <el-table-column v-if="config.selectable" type="selection" width="55">
    </el-table-column>

    <template v-for="col in cols">
      <el-table-column
        v-if="col.type == 'calc'"
        :fixed="col.fixed"
        :key="col.name"
        :prop="col.name"
        :label="col.label"
        :width="col.width"
        :min-width="col.minWidth"
        :sortable="col.sortable"
        :cell-class-name="col.class"
        :header-cell-class-name="col.headerClass"
      >
        <template slot-scope="{ row }">
          <div v-if="col.type == 'calc'" :class="col.class">
            {{ col.formula(row) }}
          </div>
        </template>
      </el-table-column>

      <el-table-column
        v-else-if="col.render"
        :key="col.name"
        :prop="col.name"
        :label="col.label"
        :width="col.width"
        :min-width="col.minWidth"
        :sortable="col.sortable"
        :cell-class-name="col.class"
        :header-cell-class-name="col.headerClass"
      >
        <template slot-scope="scope">
          <slot :name="col.name" v-bind:scope="scope">
            <span v-html="col.render(scope.row)" :class="col.class"> </span>
          </slot>
        </template>
      </el-table-column>

      <el-table-column
        v-else-if="col.type == 'custom'"
        :key="col.name"
        :prop="col.name"
        :label="col.label"
        :width="col.width"
        :min-width="col.minWidth"
        :sortable="col.sortable"
        :class-name="col.headerClass"
        :cell-class-name="col.class"
        :header-cell-class-name="col.headerClass"
      >
        <template slot-scope="scope">
          <slot :name="col.name" v-bind:scope="scope"> </slot>
        </template>
      </el-table-column>

      <el-table-column
        v-else
        :key="col.name"
        :prop="col.name"
        :label="col.label"
        :width="col.width"
        :min-width="col.minWidth"
        :sortable="col.sortable"
        :cell-class-name="col.class"
        :header-cell-class-name="col.headerClass"
      >
      </el-table-column>
    </template>

    <template v-slot:append>
      <slot name="append"> </slot>
    </template>
  </el-table>
</template>

<script>
export default {
  props: {
    cols: {
      type: Array,
      required: true
    },
    isLoading: {
      type: Boolean,
      default: false
    },
    tableData: {
      type: Array
    },
    config: {
      type: Object,
      default() {
        return {};
      }
    }
  },
  methods: {
    getHeaderClass({ row }) {
      return row.headerClass;
    }
  }
};
</script>

<style></style>
