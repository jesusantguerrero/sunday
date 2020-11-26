<template>
    <dialog-modal :show="isOpen" @close="$emit('closed')">
        <template #title>
            Add Automation
        </template>

        <template #content>
            <form action="" @submit.prevent="save">
                <div class="form-group">
                    <label for="title"> Service </label>
                        <multiselect
                            v-model="selectedService"
                            ref="input"
                            :show-labels="false"
                            :options="services"
                            class="w-full"
                        >
                        <template slot="singleLabel" slot-scope="props">
                            <div>
                                <img :src="props.option.logo" class="automation-logo mr-2" />
                                {{ props.option.name }}
                            </div>
                        </template>
                        <template slot="option" slot-scope="props">
                            <div class="d-flex">
                                <img :src="props.option.logo" class="automation-logo mr-2" />
                                {{ props.option.name }}
                            </div>
                        </template>
                        </multiselect>
                </div>
                <div class="form-group" v-if="selectedService.events">
                    <label for="title"> Events </label>
                        <multiselect
                            v-model="selectedEvent"
                            ref="input"
                            :show-labels="false"
                            label="name"
                            :options="Object.values(JSON.parse(selectedService.events))"
                            class="w-full"
                        >
                        </multiselect>
                </div>
                <div class="form-group" v-if="calendarList">
                    <label for="title"> Calendar </label>
                        <multiselect
                            v-model="formData.calendar"
                            ref="input"
                            :show-labels="false"
                            label="summary"
                            :options="calendarList"
                            class="w-full"
                        >
                        </multiselect>
                </div>
                <div class="form-group">
                    <label for="title"> Stage </label>
                        <multiselect
                            v-model="formData.stage"
                            ref="input"
                            :show-labels="false"
                            label="name"
                            :options="board.stages"
                            class="w-full"
                        >
                        </multiselect>
                </div>
                <div class="form-group" v-if="selectedService.action">
                    <label for="title"> Action </label>
                        <multiselect
                            v-model="selectedRecipe"
                            ref="input"
                            :show-labels="false"
                            label="name"
                            :options="automationRecipes"
                            class="w-full"
                        >
                        </multiselect>
                </div>
                <div class="form-group" v-if="selectedService.action">
                    <label for="title"> Action </label>
                        <multiselect
                            v-model="selectedRecipe"
                            ref="input"
                            :show-labels="false"
                            label="name"
                            :options="automationRecipes"
                            class="w-full"
                        >
                        </multiselect>
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
import DialogModal from "../../Jetstream/DialogModal"
import ItemGroupCell from "./ItemGroupCell";
import PrimaryButton from "../../Jetstream/Button"
import Draggable from "vuedraggable";
import Multiselect from "vue-multiselect";

export default {
    components: {
        ItemGroupCell,
        DialogModal,
        PrimaryButton,
        Draggable,
        Multiselect
    },
    props: {
        isOpen: {
            type: Boolean
        },
        board: {
            type: Object,
            required: true
        },
        recordData: {
            type: Object
        }
    },
    created() {
        this.getServices()
        this.getRecipies()
        this.getCalendarList();
    },
    data() {
        return {
            formData: {

            },
            newCheck: {},
            services: [],
            recipies: [],
            calendarList: [],
            selectedService: {},
            selectedEvent: {}
        }
    },
    watch: {
        recordData() {
            this.formData = this.recordData
        }
    },
    methods: {
        prepareData() {
            const formData = {...this.formData};
            this.formData.mapper
        },
        save() {
            const method = this.formData.id ? "PUT" : "POST";
            const param = this.formData.id ? `/${this.formData.id}` : "";
            axios({
                url: `/api/automations${param}`,
                method,
                data: this.formData
            }).then(() => {
                this.$emit('saved')
            });
        },

        getServices() {
            axios({
                url: "/api/automation-services"
            }).then(({data}) => {
                this.services = data;
            })
        },

        getRecipies() {
            axios({
                url: "/api/automation-recipies"
            }).then(({data}) => {
                this.recipies = data;
            })
        },

        getCalendarList() {
            axios({
                url: "/api/calendars"
            }).then(({data}) => {
                this.calendarList = data;
            })
        },

        deleteCheck(index) {
            this.formData.checklist.splice(index, 1);
        },

        addToCheckList() {
            if (this.formData.checklist) {
                this.formData.checklist.push(this.newCheck)
                this.newCheck = {};
                return
            }
            this.formData.checklist = [this.newCheck];
            this.newCheck = {};
        }
    }
}
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
            transition: all ease .5s;
            cursor: pointer;
            &:hover {
                @apply text-red-400;
            }
        }

        &__move {
            margin-right: 5px;
            color: #eee;
            transition: all ease .5s;
            cursor: pointer;
            &:hover {
                @apply text-purple-400;
            }
        }

        &__input, form-control {
            border: none;
            background: white;

            &:hover, &:focus {
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

    .workflow-item {
        @apply border-2 border-gray-300;
        display: inline-block;
        margin: 2px;
        padding: 2px 5px;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
