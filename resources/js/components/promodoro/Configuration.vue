<template>
    <dialog-modal :show="isOpen" @close="$emit('closed')">
        <template #title>
            Promodoro Configuration
        </template>

        <template #content>
            <form action="" @submit.prevent="save">
                <h1>  User preferences </h1>
                <div class="form-group">
                    <label for="">
                        <input type="checkbox" name="" id="">
                        Timer indication title
                    </label>
                </div>
                <div class="form-group">
                    <label for="">
                        <input type="checkbox" name="" id="">
                        Browser Notification
                    </label>
                </div>
                <div class="form-group">
                    <label for="">
                        <input type="checkbox" name="" id="">
                        Autostart promodoros and breaks
                    </label>
                </div>
                 <h1>  Workflow Template </h1>
                 <div class="form-group">
                    <div class="workflow-item"
                        v-for="(item, index) in promodoroTemplate"
                        :key="`template-item-${index}`"
                        @click="removeItem(index)">
                        {{ modes[item] ? modes[item].name : item }}
                        <i class="fa fa-minus"></i>
                    </div>
                 </div>
                 <h1>  Workflow Items </h1>
                 <div class="form-group">
                    <div class="workflow-item"
                        v-for="(item, key) in modes"
                        :key="key"
                        @click="addWorkflowItem(key)">
                        {{ item.name }}
                        <i class="fa fa-plus"></i>
                    </div>
                 </div>
                 <h1>  Select sound </h1>
                 <div class="form-group">
                    <select name="" id="" class="form-control">
                        <option value="">Alarm Clock</option>
                        <option value="">Elevator Ding</option>
                    </select>
                 </div>
                 <h1>  Set Times </h1>
                 <div class="flex">
                    <div class="form-group">
                        <label for="">
                            Session
                        </label>
                        <input
                            type="number"
                            min="1"
                            class="form-control"
                            v-model="modes.session.minutes">
                    </div>
                    <div class="form-group">
                        <label for="">
                            Break
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            min="1"
                            v-model="modes.break.minutes"
                        >
                    </div>
                    <div class="form-group">
                        <label for="">
                            Long Break
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            min="1"
                            v-model="modes.longBreak.minutes"
                        >
                    </div>
                 </div>
                <primary-button @click.native.prevent="playSound">
                    Test Audio
                </primary-button>
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
import PrimaryButton from "../../Jetstream/Button"
import promodoroMixin from "./promodoro"

export default {
    mixins: [promodoroMixin],
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
        }
    },
    data() {
        return {
            modes: {
                session: {
                    name: 'Session',
                    minutes: 25,
                    seconds: 0
                },
                break: {
                    name: 'Break',
                    minutes: 5,
                    seconds: 0
                },
                longBreak: {
                    name: 'Long Break',
                    minutes: 15,
                    seconds: 0
                }
            },
            promodoroTemplate: JSON.parse(localStorage.getItem('promodoroTemplate')) || []
        }
    },
    watch: {
        isOpen() {
            this.init();
        }
    },
    methods: {
        save() {
            localStorage.setItem('promodoroTemplate', JSON.stringify(this.promodoroTemplate))
            Object.keys(this.modes).forEach((key) => {
                this.modes[key].minutes = Number(this.modes[key].minutes) || 1;
            })
            localStorage.setItem('modes', JSON.stringify(this.modes))
            this.$emit('saved');
        },

        addWorkflowItem(item) {
            this.promodoroTemplate.push(item)
        },

        removeItem(index) {
            this.promodoroTemplate.splice(index, 1);
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
