<template>
    <dialog-modal :show="isOpen" @close="$emit('closed')">
        <template #title>
            Edit Item
        </template>

        <template #content>
            <form action="" @submit.prevent="save">
                <div class="form-group">
                    <label for="title"> Title </label>
                    <input
                        type="text"
                        class="form-control"
                        v-model="formData.title"
                    />
                </div>

                <div class="form-group">
                    <label for="title"> Checklist </label>
                    <draggable v-model="formData.checklist" handle=".handle">
                        <div
                            v-for="(check, index) in formData.checklist"
                            :key="check.id"
                            class="checklist-item"
                        >
                            <i
                                class="fa fa-arrows-alt checklist-item__move handle"
                            ></i>
                            <input
                                type="checkbox"
                                class="form-control-check"
                                v-model="check.done"
                            />
                            <input
                                type="text"
                                class="form-control checklist-item__input"
                                v-model="check.title"
                            />
                            <i
                                class="fa fa-trash checklist-item__delete"
                                @click="deleteCheck(index)"
                            ></i>
                        </div>
                    </draggable>
                    <item-group-cell
                        class="w-full flex items-center"
                        field-name="title"
                        :is-title="true"
                        :index="-1"
                        :item="newCheck"
                        :is-new="true"
                        @saved="newCheck['title'] = $event"
                        @keydown.enter="addToCheckList()"
                    >
                    </item-group-cell>
                </div>

                <div class="form-group" v-if="boards">
                    <label for="title"> Board </label>
                    <multiselect
                        v-model="formData.board"
                        ref="input"
                        :show-labels="false"
                        placeholder="Select board"
                        :options="boards"
                        class="w-full"
                        @input="getBoardData"
                    >
                        <template slot="singleLabel" slot-scope="props">
                            <span class="option__title">
                                <i class="fa fa-briefcase mr-2"></i>
                                {{ props.option.name }}
                            </span>
                        </template>
                        <template slot="option" slot-scope="props">
                            <div class="option__desc">
                                <span class="option__title">
                                    <i class="fa fa-briefcase mr-2"></i>
                                    {{ props.option.name }}
                                </span>
                            </div>
                        </template>
                    </multiselect>
                </div>

                <div
                    class="form-group"
                    v-if="formData.board && formData.board.stages"
                >
                    <label for="title"> Stage </label>
                    <multiselect
                        v-model="formData.stage"
                        ref="input"
                        :show-labels="false"
                        placeholder="Select board"
                        :options="formData.board.stages"
                        class="w-full"
                    >
                        <template slot="singleLabel" slot-scope="props">
                            <span class="option__title">
                                <i class="fa fa-briefcase mr-2"></i>
                                {{ props.option.name }}
                            </span>
                        </template>
                        <template slot="option" slot-scope="props">
                            <div class="option__desc">
                                <span class="option__title">
                                    <i class="fa fa-briefcase mr-2"></i>
                                    {{ props.option.name }}
                                </span>
                            </div>
                        </template>
                    </multiselect>
                </div>

                <div v-if="isLoading">
                    Loading fields...
                </div>

                <div v-if="visibleFields" class="grid grid-cols-2">
                    <div
                        v-for="(field, index) in visibleFields"
                        :key="field.name"
                        class="form-group form-cell pb-5"
                    >
                        <label for=""> {{ field.title }} </label>
                        <item-group-cell
                            :field-name="field.name"
                            :field="field"
                            :index="index"
                            :item="formData"
                            @saved="formData[field.name] = $event"
                        >
                        </item-group-cell>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <primary-button @click.native="$emit('cancel')">
                Cancel
            </primary-button>
            <primary-button @click.native="save()">
                Save
            </primary-button>
        </template>
    </dialog-modal>
</template>

<script>
import DialogModal from "../../Jetstream/DialogModal";
import ItemGroupCell from "./ItemGroupCell";
import PrimaryButton from "../../Jetstream/Button";
import Draggable from "vuedraggable";

export default {
    components: {
        ItemGroupCell,
        DialogModal,
        PrimaryButton,
        Draggable
    },
    inject: ["users"],
    props: {
        isOpen: {
            type: Boolean
        },
        recordData: {
            type: Object
        },
        type: {
            type: String
        },
        boards: {
            type: Array
        }
    },
    data() {
        return {
            isLoading: false,
            formData: {},
            newCheck: {}
        };
    },
    watch: {
        recordData() {
            this.formData = this.recordData;
        }
    },
    computed: {
        visibleFields() {
            if (this.formData.board && this.formData.board.fields) {
                return this.formData.board.fields
                .map(field => {
                    field.order = this.fieldOrder.findIndex(fieldname => field.name == fieldname);
                    return field;
                })
                .filter(field => !field.hide)
                .sort((a, b) => a.order - b.order)
            }
            return []
        },
        fieldOrder() {
            const fields = {
                event: [
                    "owner",
                    "status",
                    "priority",
                    "date",
                    "time",
                    "due_date",
                    "end_time"
                ]
            }

            return fields[this.type] || [];
        },
        typeFields() {
            const fields = {
                event: [
                    {name : 'date', 'type': 'date', title: "Date"},
                    {name : 'time', 'type': 'time', title: "Time"},
                    {name : 'due_date', 'type': 'date',title: "Due Date" },
                    {name : 'end_time', 'type': 'time', title: "End Time"}
                ]
            }
            return fields[this.type] || [];
        }
    },
    methods: {
        prepareForm() {
            const formData = { ...this.formData }
            if (this.formData.board) {
                formData.board_id = this.formData.board.id
            }

            if (this.formData.stage) {
                formData.stage_id = this.formData.stage.id
            }

            delete formData.board
            delete formData.stage
            return formData
        },

        save() {
            const method = this.formData.id ? "PUT" : "POST";
            const param = this.formData.id ? `/${this.formData.id}` : "";
            const formData = this.prepareForm();

            if (!formData.title || !formData.board_id) {
                this.$notify({
                    type: "info",
                    message: `Board and title are required`,
                });
                return
            }

            formData.fields = this.formData.board.fields.map(field => {
                return {
                    field_id: field.id,
                    field_name: field.name,
                    name: field.name,
                    type: field.type,
                    value: formData[field.name],
                }
            })

            axios({
                url: `/items${param}`,
                method,
                data: formData
            }).then(() => {
                this.$emit("saved");
            });
        },

        getBoardData() {
            this.isLoading = true
            Promise.all([
                axios({
                    url: `/api/stages/`,
                    params: {
                        "filter[board_id]": this.formData.board.id
                    }
                }),
                axios({
                    url: "/api/fields",
                    params: {
                        "filter[board_id]": this.formData.board.id
                    }
                })
            ]).then(([stagesResponse, fieldsResponse]) => {
                let fields = fieldsResponse.data.data
                const stages = stagesResponse.data.data
                this.$set(
                    this.formData.board,
                    "stages",
                    stages
                );

                this.$set(
                    this.formData,
                    "stage",
                    stages[0]
                );

                const fieldNames = fields.map(field => field.name);
                this.typeFields.forEach((field) => {
                    if (!fieldNames.includes(field.name)) {
                        fields.push(field);
                    }
                });


                this.$set(
                    this.formData.board,
                    "fields",
                    fields
                );
                this.isLoading = false
            });
        },

        deleteCheck(index) {
            this.formData.checklist.splice(index, 1);
        },

        addToCheckList() {
            if (this.formData.checklist) {
                this.formData.checklist.push(this.newCheck);
                this.newCheck = {};
                return;
            }
            this.formData.checklist = [this.newCheck];
            this.newCheck = {};
        }
    }
};
</script>

<style lang="scss">
.form-control {
    @apply w-full bg-gray-100 border-gray-400 border-2 px-4;
    height: 37px;
    border-radius: 4px;
}

.checklist-item {
    display: flex;
    align-items: center;
    border: 1px solid #eee;
    border-left: 0;
    border-right: 0;
    padding: 0 5px;

    &__delete {
        color: #eee;
        transition: all ease 0.5s;
        cursor: pointer;
        &:hover {
            @apply text-red-400;
        }
    }

    &__move {
        margin-right: 5px;
        color: #eee;
        transition: all ease 0.5s;
        cursor: pointer;
        &:hover {
            @apply text-purple-400;
        }
    }

    &__input,
    form-control {
        border: none;
        background: white;

        &:hover,
        &:focus {
            outline: none;
        }
    }
}

h1 {
    @apply mb-2;
}

.form-group {
    @apply mx-2 mb-4;
}

.form-cell {
    .item-group-cell {
        @apply border-2 border-gray-200 px-0;

        span {
            height: 100%;
            display: flex;
            align-items: center;
        }
        height: 37px;

    }
}

.workflow-item {
    @apply border-2 border-gray-300;
    display: inline-block;
    margin: 2px;
    padding: 2px 5px;
    border-radius: 4px;
    cursor: pointer;
}
</style>
