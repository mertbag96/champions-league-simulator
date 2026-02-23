<script setup lang="ts">
import { usePage, Link } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { getTeamLogoUrl } from '@/lib/teamLogo';
import { index as fixtures, store } from '@/routes/fixtures';
import { index } from '@/routes/teams';
import type { Team } from '@/types/team';

const page = usePage();

const toast = useToast();

const appName = import.meta.env.VITE_APP_NAME || 'Champions League Simulator';

const props = defineProps<{
  teams: Team[]
  readyForSimulation: boolean
  fixturesGenerated: boolean
}>()

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
        <!-- Homepage -->
        <main class="w-[90%] xs:container mx-auto py-6 sm:py-12 flex flex-col justify-center items-center">
            <div class="w-full flex flex-col items-center space-y-6 md:space-y-8 max-w-2xl">
                <!-- Title -->
                <h1 class="font-semibold text-lg sm:text-2xl text-center">
                    Welcome to UEFA {{ appName }}
                </h1>

                <!-- Description -->
                <p class="text-gray-300 text-sm sm:text-base text-center leading-relaxed">
                    Simulate a 4-team group stage (minimum 4 teams) where match results are determined by team strength.
                    Manage teams, generate fixtures, and simulate matches progressively or all at once. Only active teams
                    can be used for simulation.
                </p>

                <!-- Manage Teams Button -->
                <Link
                    v-if="!props.readyForSimulation"
                    :href="index.url()"
                    class="p-2 rounded-sm border border-white bg-white text-blue-800 hover:bg-transparent hover:text-white font-semibold text-sm transition-all duration-200 cursor-pointer"
                >
                    Manage teams to start simulating
                </Link>

                <!-- Simulation -->
                <div
                    v-else
                    class="flex flex-col space-y-6 md:space-y-8"
                >
                    <!-- Table Container -->
                    <div class="mt-4 w-full overflow-hidden rounded-lg border border-blue-800/75 bg-blue-800/25 shadow-xl backdrop-blur-sm">
                        <!-- Table -->
                        <table class="w-full border-collapse">
                            <!-- Head -->
                            <thead>
                                <tr class="border-b border-blue-800 bg-blue-800">
                                    <th class="px-4 sm:px-6 py-4 text-left text-sm font-semibold text-gray-200">Team Name</th>
                                    <th class="px-4 sm:px-6 py-4 text-right text-sm font-semibold text-gray-200">Team Strength</th>
                                </tr>
                            </thead>

                            <!-- Body -->
                            <tbody class="divide-y divide-blue-600/50">
                                <tr
                                    v-for="team in props.teams"
                                    :key="team.id"
                                    class="bg-gray-800/20 hover:bg-blue-900/20 transition-colors duration-150"
                                >
                                    <td class="px-4 sm:px-6 py-3.5 text-sm font-medium text-white">
                                        <span class="flex items-center gap-2">
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
                                    <td class="px-4 sm:px-6 py-3.5 text-right text-sm font-semibold tabular-nums text-blue-400">{{ team.power }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- See Fixtures -->
                    <Link
                        v-if="fixturesGenerated"
                        :href="fixtures.url()"
                        class="p-2 rounded-sm border border-white bg-white text-blue-800 hover:bg-transparent hover:text-white font-semibold text-sm text-center transition-all duration-200 cursor-pointer"
                    >
                        See Fixtures
                    </Link>

                    <!-- Generate Fixtures Button -->
                    <Link
                        v-else
                        :href="store.url()"
                        method="post"
                        as="button"
                        class="p-2 rounded-sm border border-white bg-white text-blue-800 hover:bg-transparent hover:text-white font-semibold text-sm transition-all duration-200 cursor-pointer"
                    >
                        Generate Fixtures
                    </Link>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
