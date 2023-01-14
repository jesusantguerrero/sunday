<template>
<div  class="task-item">
    <div>
        <label class="checkbox-label">
            <span class="font-bold">
            <inertia-link :href="`/boards/${task.board_id}`">
                <span class="font-bold">
                    <i class="mx-2 fas fa-layer-group"></i>
                    {{ task.stage }}
                </span>
            </inertia-link>
        </span>
        <span>
            {{ task.title }}
        </span>
        </label>
    </div>
    <div class="flex items-center actions-container">
        <input
            type="checkbox"
            v-model="task.done"
            name=""
            class="checkbox-done"
            :id="task.id"
            @change="updateTask(task)"
        />
        <el-tooltip class="item" effect="dark" :content="task.priority || 'none'" placement="left">
            <div class="inline-block ml-2 mr-4 priority-level">
                <div class="priority-level__inner">

                </div>
            </div>
        </el-tooltip>
        <span class="mr-2">
            {{ durationFromMs }}
        </span>
        <button @click="$emit('item-clicked', task)" class="play-button" v-if="!task.commit_date">
            <i class="fa" :class="[isTracker ? 'fa-pause' : 'fa fa-play']"/>
        </button>

        <button @click="$emit('item-deleted', task)" class="ml-2 text-gray-300 hover:text-red-400">
            <i class="fa fa-trash"/>
        </button>
    </div>
</div>
</template>

<script>
import Tracker from "./../timeTracker/tracker";

export default {
    props: {
        task: {
            type: Object,
            required: true
        },
        tracker: {
            type: Object
        }
    },

    data() {
        return {
            originalDuration: null
        }
    },

    watch: {
        tracker(tracker) {
            if (tracker) {
                this.originalDuration = this.task.duration
            } else {
                this.originalDuration = this.task.duration
            }
        }
    },

    created() {
        this.originalDuration = this.task.duration;
    },

    computed: {
        isTracker() {
            return this.tracker && this.tracker.timeEntry.item_id == this.task.id;
        },
        durationFromMs() {
            const currentDuration = this.isTracker ? this.tracker.duration || 0: 0;

            this.task['duration'] = this.originalDuration + currentDuration;
            return Tracker.durationFromMs(this.task.duration);
        },
        priorityText() {
            const emojis = {
                "high": "🔥🔥🔥",
                "medium": "🔥🔥",
                "low": "🔥"
            }
            return this.task && emojis[this.task.priority] || "";
        }
    },
    methods: {
         updateTask() {
            this.$emit('update-item', this.task)
        },
    }
}
</script>

<style lang="scss" scoped>
.priority-level {
    --color: red;
    border: solid 1px transparent;
    border-radius: 0.20rem;
    padding: 2px;
    transition: all ease .3s;

    &:hover {
        border: solid 1px var(--color);
    }
    &__inner {
        background-color: var(--color);
        width: 12px;
        height: 12px;
        border-radius: 0.20rem;
    }
}
</style>
