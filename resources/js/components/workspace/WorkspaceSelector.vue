<template>
<div class="px-5">
    <div class="flex justify-between">
        <h4 class="text-sm font-bold">Workspace</h4>
        <div>
            <DropdownList
                align="right"
                width="48"
                :actions="workspaceOptions"
            >
                <template #trigger>
                    <button
                        class="flex text-sm transition duration-150 ease-in-out border-2 border-transparent rounded-md rounded-full hover:bg-gray-300 focus:outline-none focus:border-gray-300"
                    >
                        <IconEllipsis />
                    </button>
                </template>
            </DropdownList>
        </div>
    </div>
    <div class="mt-2">
        <multiselect
            :value="currentWorkspace"
            :show-labels="false"
            label="name"
            :options="workspaceList"
            class="w-full"
            @select="switchToWorkspace"
        >
            <template v-slot:singleLabel="{ option }">
                <WorkspaceListItem :option="option" />
            </template>
            <template v-slot:option="{ option }">
                <WorkspaceListItem :option="option" />
            </template>
            <template #afterList>
                <div class="flex justify-between text-xs border-t border-gray-400">
                    <button role="button" class="px-2 py-1 rounded-md cursor-pointer hover:bg-gray-200" @click="isWorkspaceFormOpen=true">Add Workspace</button>
                    <button role="button" class="px-2 py-1 rounded-md cursor-pointer hover:bg-gray-200">Workspaces</button>
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
import IconEllipsis from '../icons/IconEllipsis.vue';
import DropdownLink from '../../Jetstream/DropdownLink.vue';
import Dropdown from '../../Jetstream/Dropdown.vue';
import DropdownList from '../DropdownList.vue';
import WorkspaceIcon from './WorkspaceIcon.vue';
import WorkspaceListItem from './WorkspaceListItem.vue';

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
            isWorkspaceFormOpen: false,
            workspaceOptions: [
                {
                    name: 'rename',
                    label: 'Rename Workspace'
                },
                {
                    name: 'change_icon',
                    label: 'Change Icon'
                },
                {
                    name: 'manage',
                    label: 'Manage Workspace'
                },
                {
                    name: 'delete',
                    label: 'Delete Workspace'
                },
                '',
                {
                    name: 'add',
                    label: 'Add Workspace'
                },
                {
                    name: 'browse',
                    label: 'Browse all workspace'
                },
                '',
                {
                    name: 'collapse_folders',
                    label: 'Collapse Folders'
                }
            ]
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
        },
        switchToWorkspace(workspace) {
            this.$inertia.put(
                "/current-workspace",
                {
                    workspace_id: workspace.id
                },
                {
                    preserveState: false
                }
            );
        },
    },
    components: { WorkspaceForm, IconEllipsis, DropdownLink, Dropdown, DropdownList, WorkspaceIcon, WorkspaceListItem }
}
</script>
