<template>
    <dialog-modal :show="isOpen" @close="$emit('closed')">
        <template #title>
            <span class="text-lg">
                Add Automation
            </span>
        </template>

        <template #content>
            <form action="" @submit.prevent="save">
                <div class="form-group">
                    <label for="title"> Service </label>
                    <multiselect
                        v-model="formData.service"
                        ref="input"
                        :show-labels="false"
                        :options="services"
                        class="w-full"
                    >
                        <template slot="singleLabel" slot-scope="props">
                            <div>
                                <img
                                    :src="props.option.logo"
                                    class="mr-2 automation-logo"
                                />
                                {{ props.option.name }}
                            </div>
                        </template>
                        <template slot="option" slot-scope="props">
                            <div class="d-flex">
                                <img
                                    :src="props.option.logo"
                                    class="mr-2 automation-logo"
                                />
                                {{ props.option.name }}
                            </div>
                        </template>
                    </multiselect>
                </div>

                <div class="form-group" v-if="serviceIntegrations">
                    <label for="title"> Connection </label>
                    <multiselect
                        v-model="formData.integration"
                        ref="input"
                        :show-labels="false"
                        label="hash"
                        :options="serviceIntegrations"
                        class="w-full"
                    >
                    </multiselect>
                </div>

                <div class="form-group" v-if="formData.service">
                    <label for="title"> Recipe </label>
                    <multiselect
                        v-model="formData.recipe"
                        ref="input"
                        :show-labels="false"
                        label="name"
                        :options="automationRecipes"
                        class="w-full"
                    />
                </div>

                <div class="form-group" v-if="boards">
                    <label for="title"> Board </label>
                    <multiselect
                        v-model="formData.board"
                        ref="input"
                        :show-labels="false"
                        :allow-empty="false"
                        placeholder="Select board"
                        :options="boards"
                        class="w-full"
                        @input="getBoardData"
                    >
                        <template slot="singleLabel" slot-scope="props">
                            <span class="option__title">
                                <i class="mr-2 fa fa-briefcase"></i>
                                {{ props.option.name }}
                            </span>
                        </template>
                        <template slot="option" slot-scope="props">
                            <div class="option__desc">
                                <span class="option__title">
                                    <i class="mr-2 fa fa-briefcase"></i>
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
                        :allow-empty="false"
                        placeholder="Select board"
                        :options="formData.board.stages"
                        class="w-full"
                    >
                        <template slot="singleLabel" slot-scope="props">
                            <span class="option__title">
                                <i class="mr-2 fa fa-briefcase"></i>
                                {{ props.option.name }}
                            </span>
                        </template>
                        <template slot="option" slot-scope="props">
                            <div class="option__desc">
                                <span class="option__title">
                                    <i class="mr-2 fa fa-briefcase"></i>
                                    {{ props.option.name }}
                                </span>
                            </div>
                        </template>
                    </multiselect>
                </div>

                <div class="form-group" v-if="hasInput('condition')">
                    <label for="title"> Condition </label>
                    <multiselect
                        v-model="formData.Condition"
                        ref="input"
                        :show-labels="false"
                        label="name"
                        :options="emailConditions"
                        class="w-full"
                    >
                    </multiselect>
                </div>

                <div class="form-group" v-if="hasInput('value')">
                    <label for="title"> value </label>
                    <input
                        v-model="formData.value"
                        type="text"
                        class="w-full form-control"
                    />
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
import DialogModal from "../Jetstream/DialogModal.vue";
import PrimaryButton from "../Jetstream/Button.vue";

export default {
    components: {
        DialogModal,
        PrimaryButton
    },
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
            formData: {
                service: null,
                recipe: null
            },
            newCheck: {},
            services: [],
            recipes: [],
            integrations: [],
            calendarList: [],
            emailConditions: [
                {
                    id: "from:",
                    name: "From"
                },
                {
                    name: "To",
                    id: "to:"
                },
                {
                    name: "subject:",
                    id: "Subject"
                },
                {
                    name: "Includes",
                    id: "has"
                },
                {
                    id: "",
                    name: "Custom"
                }
            ]
        };
    },
    created() {
        this.getServices();
        this.getIntegrations();
        this.getRecipes();
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
                        field.order = this.fieldOrder.findIndex(
                            fieldname => field.name == fieldname
                        );
                        return field;
                    })
                    .filter(field => !field.hide)
                    .sort((a, b) => a.order - b.order);
            }
            return [];
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
            };

            return fields[this.type] || [];
        },
        typeFields() {
            const fields = {
                event: [
                    { name: "date", type: "date", title: "Date" },
                    { name: "time", type: "time", title: "Time" },
                    { name: "due_date", type: "date", title: "Due Date" },
                    { name: "end_time", type: "time", title: "End Time" }
                ]
            };
            return fields[this.type] || [];
        },
        automationRecipes() {
            return this.formData.service
                ? this.recipes.filter(
                      recipe =>
                          recipe.automation_service_id ==
                          this.formData.service.id
                  )
                : [];
        },
        serviceIntegrations() {
            return this.formData.service
                ? this.integrations.filter(
                      integration =>
                          integration.automation_service_id ==
                          this.formData.service.id
                  )
                : [];
        }
    },
    methods: {
        prepareForm() {
            const formData = { ...this.formData };
            formData.automation_recipe_id = this.formData.recipe.id;
            formData.name = this.formData.recipe.name;
            formData.description = this.formData.recipe.name;
            formData.sentence = this.formData.recipe.name;

            formData.config = {};
            if (this.formData.integration) {
                formData.integration_id = this.formData.integration.id;
            }

            if (this.formData.board) {
                formData.board_id = this.formData.board.id;
            }

            if (this.formData.stage) {
                formData.config["stage_id"] = this.formData.stage.id;
            }

            if (this.formData.Condition) {
                formData.config["condition"] = this.formData.Condition.id;
            }

            const inputs = this.getInputs();
            if (inputs) {
                inputs.map((inputName) => {
                    if(!formData.config[inputName]) {
                        formData.config[inputName] = formData[inputName];
                    }
                })
            }

            formData.config = JSON.stringify(formData.config);
            delete formData.board;
            delete formData.stage;
            return formData;
        },

        save() {
            const method = this.formData.id ? "PUT" : "POST";
            const param = this.formData.id ? `/${this.formData.id}` : "";
            const formData = this.prepareForm();

            if (!formData.board_id) {
                this.$notify({
                    type: "info",
                    message: `Board and title are required`
                });
                return;
            }

            axios({
                url: `/api/automations${param}`,
                method,
                data: formData
            }).then(() => {
                this.$emit("saved");
            });
        },

        getBoardData() {
            this.isLoading = true;
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
                let fields = fieldsResponse.data.data;
                const stages = stagesResponse.data.data;
                this.formData.board["stages"] = stages;

                this.formData["stage"] = stages[0];

                const fieldNames = fields.map(field => field.name);
                this.typeFields.forEach(field => {
                    if (!fieldNames.includes(field.name)) {
                        fields.push(field);
                    }
                });

                this.formData.board["fields"] = fields;
                this.isLoading = false;
            });
        },

        getServices() {
            axios({
                url: "/api/automation-services"
            }).then(({ data }) => {
                this.services = data;
            });
        },

        getIntegrations() {
            axios({
                url: "/api/integrations"
            }).then(({ data }) => {
                this.integrations = data.data;
            });
        },

        getRecipes() {
            axios({
                url: "/api/automation-recipes"
            }).then(({ data }) => {
                this.recipes = data;
            });
        },

        getCalendarList() {
            axios({
                url: "/api/calendars"
            }).then(({ data }) => {
                this.calendarList = data;
            });
        },

        getInputs() {
            if (this.formData.recipe) {
                const mapper = JSON.parse(this.formData.recipe.mapper);
                return mapper && mapper.input
            }
        },

        hasInput(inputName) {
            const inputs = this.getInputs();
            if (inputs) {
                return inputs.includes(inputName);
            }
        }
    }
};
</script>

<style lang="scss" scoped>
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

.automation-logo {
    width: 20px;
    height: 20px;
    padding: 2px;
    display: inline-block;
    cursor: pointer;
    border-radius: 50%;
    background: white;
    border: 1px solid crimson;
}
</style>
