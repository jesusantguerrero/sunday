<template>
<div class="px-5">
    <div class="flex justify-between">
        <h4>Workspace</h4>
        <div>Options</div>
    </div>
    <div>
        <multiselect
            :value="currentWorkspace"
            :show-labels="false"
            label="name"
            :options="workspaceList"
            class="w-full"
        >
            <template #afterList>
                <div class="flex justify-between px-4 py-2 text-xs border-t border-gray-400">
                    <span role="button" class="cursor-pointer" @click="isWorkspaceFormOpen=true">Add Workspace</span>
                    <span role="button" class="cursor-pointer">Workspaces</span>
                </div>
            </template>
        </multiselect>

        <WorkspaceForm
            :is-open="isWorkspaceFormOpen"
            @saved="addWorkspace"
            @cancel="isWorkspaceFormOpen=false"
        />
    </div>
</div>
</template>

<script>
import WorkspaceForm from './WorkspaceForm.vue';
export default {
    props: {
        currentWorkspace: {
            type: Object,
            required: true
        },
        workspaces: {
            type: Array,
            default() {
                return [];
            }
        }
    },
    data() {
        return {
            isWorkspaceFormOpen: false
        };
    },
    computed: {
        workspaceList() {
            return [...this.workspaces];
        }
    },
    methods: {
        addWorkspace() {
            this.isWorkspaceFormOpen = false;
            this.$inertia.reload({
                preserveScroll: true,
                only: ['user']
            })
        }
    },
    components: { WorkspaceForm }
}
</script>
