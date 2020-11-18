<template>
<el-popover
    placement="bottom"
    width="200"
    trigger="click">
    <div class="field-popover">
        <input type="text" class="form-control" v-model="field.title" @keydown.enter="addField()">
        <div class="form-group"  v-if="!field.id || field.manual">
            <label for="">Property Type</label>
            <el-select
                v-model="field.type"
                placeholder="Select"
                ref="input"
                :select-first="true"
                :filterable="true"
                :automatic-dropdown="true"
            >
                <el-option
                    v-for="item in fieldTypes"
                    :key="item.name"
                    :label="item.title"
                    :value="item.name">
                        <div>
                            <i :class="item.icon" class="mr-2"/>
                            <span>{{ item.title }}</span>
                        </div>
                </el-option>
            </el-select>
        </div>
        <ul class="options-list">
            <li class="option-list__item">
                <button class="option-list__button"> Duplicate</button>
            </li>
            <li class="option-list__item" v-if="field.manual">
                <button  @click="deleteField()"  class="option-list__button hover:bg-red-300"> Delete</button>
            </li>
            <li class="option-list__item">
                <button  @click="addField()"  class="option-list__button hover:bg-green-300"> Save </button>
            </li>
        </ul>
    </div>
    <button slot="reference">
        <slot>
            <i class="fa fa-plus" ></i>
        </slot>
    </button>
</el-popover>
</template>

<script>
export default {
    props: {
        fieldData: {
            type: Object,
            required: true
        },
        board: {
            type: Object,
            required: true
        }
    },
    watch: {
        field: {
            handler() {
                this.field = {...this.fieldData}
                if (!this.field.type) {
                    this.$set(this.field, 'type', "text");
                }
            },
            immediate: true
        },
    },
    data() {
        return {
            fieldTypes: [
                {
                    name: "text",
                    icon: "fa fa-align-left",
                    title: "Text"
                },
                {
                    name: "date",
                    icon: "far fa-calendar-alt",
                    title: "Date"
                },
                {
                    name: "time",
                    icon: "far fa-clock",
                    title: "Time"
                },
                {
                    name: "number",
                    icon: "fa fa-hashtag",
                    title: "Number"
                },
                {
                    name: "label",
                    icon: "fa fa-tags",
                    title: "Selects",
                },
                {
                    name: "person",
                    icon: "fa fa-user-friends",
                    title: "Person"
                }
            ]
        }
    },
    methods: {
        addField() {
            const field = {
                board_id: this.board.id,
                name: this.field.name || this.field.title || `field_${this.board.fields.length}`,
                title: this.field.title || this.field.name,
                type: this.field.type,
                manual: this.field.manual || true
            }
            let method = "POST";
            let url = "/api/fields"

            if (this.field.id) {
                method = "put";
                url += `/${this.field.id}`
            }

            axios({
                    url,
                    method,
                    data: field
                }).then(() => {
                    this.$emit('saved')
                });
        },
        deleteField() {
            axios({
                    url: `/api/fields/${this.field.id}`,
                    method: "delete",
                }).then(() => {
                    this.$emit('saved')
                });
        },
    }
}
</script>

<style lang="scss" scoped>
    .form-group {
        margin: 5px 0;
        border-bottom: 1px solid #ddd;
        padding: 5px 0;
    }

    .option-list {
        list-style: none;
        margin: 0;
        padding: 5px 0;
        margin-top: 5px;

        &__item {
            width: 100%;
            height: 34px;
            padding: 5px 0;
        }

        &__button {
            width: 100%;
            height: 34px;
            padding: 0 5px;
            text-align: left;

            &:hover {
                @apply bg-blue-200;
            }
        }
    }
</style>
