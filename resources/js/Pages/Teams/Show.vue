<template>
    <app-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Team Settings
            </h2>
        </template>

        <div>
            <div class="py-10 mx-auto max-w-8xl sm:px-6 lg:px-8">
                <update-team-name-form :team="team" :permissions="permissions" />

                <team-member-manager class="mt-10 sm:mt-0"
                    :team="team"
                    :available-roles="availableRoles"
                    :user-permissions="permissions"
                />

                <template v-if="permissions.canDeleteTeam && ! team.personal_team">
                    <jet-section-border />

                    <delete-team-form class="mt-10 sm:mt-0" :team="team" />
                </template>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import TeamMemberManager from './TeamMemberManager.vue'
    import AppLayout from './../../Layouts/AppLayout.vue'
    import DeleteTeamForm from './DeleteTeamForm.vue'
    import JetSectionBorder from './../../Jetstream/SectionBorder.vue'
    import UpdateTeamNameForm from './UpdateTeamNameForm.vue'

    export default {
        props: [
            'team',
            'availableRoles',
            'permissions',
        ],

        components: {
            AppLayout,
            DeleteTeamForm,
            JetSectionBorder,
            TeamMemberManager,
            UpdateTeamNameForm,
        },
    }
</script>
