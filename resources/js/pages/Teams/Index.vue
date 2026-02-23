<script setup lang="ts">
import { router, Link, usePage } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { getTeamLogoUrl } from '@/lib/teamLogo';
import { create, edit, destroy } from '@/routes/teams';
import type { Team } from '@/types/team';

const page = usePage();

const toast = useToast();

const props = defineProps<{
  teams: Team[]
}>()

const showDeleteModal = ref(false);
const teamToDelete = ref<Team | null>(null);

const openDeleteModal = (team: Team) => {
  teamToDelete.value = team;
  showDeleteModal.value = true;
}

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  teamToDelete.value = null;
}

const confirmDelete = () => {
  if (!teamToDelete.value) return;

  router.delete(destroy.url({ team: teamToDelete.value.id }), {
    onSuccess: () => {
      closeDeleteModal();
    },
    onError: () => {
      toast.error('An error occured while deleting team!');
    },
  })
}

watchEffect(() => {
  const success = page.props.flash?.success;
  const error = page.props.flash?.error;

  if (success) {
    toast.success(success);
  }

  if (error) {
    toast.error(error);
  }
})
</script>

<template>
    <AppLayout>
        <main class="w-[90%] xs:container mx-auto py-6 sm:py-12 flex flex-col items-center space-y-4">
            <!-- Navigation -->
            <div class="mt-4 w-full xl:w-3/4 2xl:w-1/2 flex justify-between items-end">
                <!-- Title -->
                <h1 class="font-semibold text-lg md:text-2xl">All Teams</h1>

                <!-- Create Team -->
                <Link
                    :href="create.url()"
                    class="p-2 rounded-sm border border-white bg-white text-blue-800 hover:bg-transparent hover:text-white font-semibold text-sm transition-all duration-200 cursor-pointer"
                >
                    <font-awesome-icon
                        icon="fa-solid fa-circle-plus"
                    />
                    Create Team
                </Link>
            </div>

            <!-- Table Container -->
            <div class="overflow-auto w-full xl:w-3/4 2xl:w-1/2 rounded-lg border border-blue-800/75 bg-blue-800/25 shadow-xl backdrop-blur-sm">
                <!-- Table -->
                <table class="w-full border-collapse text-left overflow-auto">
                    <!-- Head -->
                    <thead class="font-semibold text-gray-200">
                        <tr class="border-b border-blue-800 bg-blue-800">
                            <th class="min-w-[50px] px-4 sm:px-6 py-4 text-sm">#</th>
                            <th class="min-w-[240px] px-4 sm:px-6 py-4 text-sm">Name</th>
                            <th class="min-w-[100px] px-4 sm:px-6 py-4 text-sm">Strength</th>
                            <th class="min-w-[100px] px-4 sm:px-6 py-4 text-sm">Active</th>
                            <th class="min-w-[20px] px-4 sm:px-6 py-4 text-sm">Edit</th>
                            <th class="min-w-[20px] px-4 sm:px-6 py-4 text-sm">Delete</th>
                        </tr>
                    </thead>

                    <!-- Body -->
                    <tbody
                        v-if="props.teams.length > 0"
                        class="divide-y divide-blue-600/50 font-medium text-white"
                    >
                        <tr
                            v-for="team in props.teams"
                            :key="team.id"
                            class="bg-gray-800/20 hover:bg-blue-900/20 transition-colors duration-150"
                        >
                            <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.id }}</td>
                            <td class="px-4 sm:px-6 py-3.5 text-sm">
                                <span class="flex items-center">
                                    <img
                                        :src="getTeamLogoUrl(team.name)"
                                        :alt="team.name"
                                        width="36"
                                        height="36"
                                        class="shrink-0 rounded object-cover"
                                        loading="lazy"
                                        @error="($event.target as HTMLImageElement).style.display = 'none'"
                                    >
                                    <span>{{ team.name }}</span>
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.power }}</td>
                            <td class="px-4 sm:px-6 py-3.5 text-sm">
                                <font-awesome-icon
                                    v-if="team.active"
                                    icon="fa-solid fa-circle-check"
                                    class="text-green-400"
                                />
                                <font-awesome-icon
                                    v-else
                                    icon="fa-solid fa-circle-xmark"
                                    class="text-red-400"
                                />
                            </td>
                            <td class="px-4 sm:px-6 py-3.5 text-sm">
                                <Link :href="edit.url({ team: team.id })">
                                    <font-awesome-icon
                                        icon="fa-solid fa-pen-to-square"
                                        class="text-blue-200"
                                    />
                                </Link>
                            </td>
                            <td class="px-4 sm:px-6 py-3.5 text-sm">
                                <button
                                    @click="openDeleteModal(team)"
                                    class="cursor-pointer"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-trash-can"
                                        class="text-red-400"
                                    />
                                </button>
                            </td>
                        </tr>
                    </tbody>

                    <tbody v-else>
                        <tr class="font-semibold">
                            <td colspan="6" class="px-4 sm:px-6 py-2.5">
                                No teams found!
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Note -->
            <p class="block md:hidden italic text-center text-xs sm:text-sm">
                Please scroll right to see the rest of the table
            </p>

            <!-- Delete Confirmation Modal -->
            <div
                v-if="showDeleteModal"
                class="fixed inset-0 bg-blue/70 flex items-center justify-center z-50 backdrop-blur-xs p-4"
                @click.self="closeDeleteModal"
            >
                <div class="bg-blue-950 border border-blue-700/50 rounded-lg shadow-lg max-w-md w-full overflow-hidden text-xs sm:text-sm md:text-md text-white">
                    <!-- Header -->
                    <div class="px-4 md:px-6 py-2.5 md:py-4 border-b border-blue-700/50">
                        <h3 class="font-semibold">Confirm Deletion</h3>
                    </div>

                    <!-- Body -->
                    <div class="bg-blue-900/50 px-4 md:px-6 py-2.5 md:py-4 space-y-4">
                        <p>Are you sure you want to delete this team?</p>

                        <p class="text-gray-400">This action cannot be undone. The team and all related data will be permanently deleted.</p>
                    </div>

                    <!-- Footer -->
                    <div class="px-4 md:px-6 py-2.5 md:py-4 border-t border-blue-700/50 flex justify-between text-center font-semibold">
                        <button
                            @click="closeDeleteModal"
                            class="w-[48%] py-2.5 rounded-sm border border-gray-700 bg-gray-800 hover:bg-gray-700 hover:text-white transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>

                        <button
                            @click="confirmDelete"
                            class="w-[48%] py-2.5 rounded-sm bg-red-600 hover:bg-red-700 transition-colors flex justify-center items-center gap-2 cursor-pointer"
                        >
                            <font-awesome-icon icon="fa-solid fa-trash-can" />
                            Delete Team
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
