<template>
  <multiselect
    v-model="localResource"
    :label="label"
    :track-by="resourceId"
    :placeholder="placeholder"
    open-direction="bottom"
    :group-values="groupValues"
    :group-label="group"
    :options="options"
    :searchable="true"
    :loading="isLoading"
    :internal-search="false"
    :options-limit="300"
    :limit="3"
    :max-height="600"
    :show-no-results="false"
    :show-labels="false"
    @open="searchResource()"
    @search-change="searchResource"
    @input="emitId"
  >
  </multiselect>
</template>

<script>
export default {
  props: {
    resourceUrl: {
      type: String,
      required: true
    },
    placeholder: String,
    label: {
      type: String,
      default: "name"
    },
    resourceId: String,
    group: String,
    groupValues: String,
    resource: {
      type: Object,
      default() {
        return {};
      }
    }
  },
  data() {
    return {
      isLoading: false,
      options: [],
      localResource: null
    };
  },
  watch: {
    resource: {
      handler(resource) {
        this.localResource = resource;
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    searchResource(query = "") {
      const url = this.resourceUrl.replace("${q}", query);
      this.isLoading = true;

      this.$http
        .get(url)
        .then(({ data }) => {
          if (data) {
            this.isLoading = false;
            this.options = data;
          }
        })
        .catch(() => {
          this.isLoading = false;
        });
    },
    emitId(resource) {
      const resourceId = resource ? resource[this.resourceId] : resource;
      this.$emit("input", resourceId);
      this.$emit("native-input", resource);
    }
  }
};
</script>
