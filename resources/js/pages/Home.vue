<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { slugify } from '@/lib/utils';
import { home } from '@/routes';
import type { Team } from '@/types/team';

const appName = import.meta.env.VITE_APP_NAME || 'Champions League Simulator';

const props = defineProps<{
  teams: Team[]
  fixturesGenerated: boolean
}>()

const teamImageModules = import.meta.glob<{ default: string }>(
  '../../assets/images/teams/*.png',
  { eager: true, query: '?url', import: 'default' }
)

const basePath = '../../assets/images/teams/';

function teamLogoUrl(teamName: string): string | undefined {
  const key = `${basePath}${slugify(teamName)}.png`
  const mod = teamImageModules[key]
  return mod != null ? (typeof mod === 'string' ? mod : (mod as { default: string }).default) : undefined
}
</script>

<template>
    <AppLayout>
        <!-- Homepage -->
        <main class="w-[90%] xs:container mx-auto py-6 sm:py-12 flex flex-col justify-center items-center">
            <div class="flex flex-col items-center space-y-6 md:space-y-8 max-w-xl">
                <!-- Title -->
                <h1 class="font-semibold text-lg sm:text-2xl text-center">
                    Welcome to UEFA {{ appName }}
                </h1>

                <!-- Description -->
                <p class="text-gray-300 text-sm sm:text-base text-center leading-relaxed">
                    Build and simulate a 4-team group stage where match results are determined by team strength.
                    Manage teams, generate fixtures, and simulate matches progressively or all at once.
                </p>

                <!-- CTA Button -->
                <Link
                    v-if="props.teams.length < 4"
                    :href="home.url()"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-sm bg-white text-blue-800 font-semibold text-sm hover:bg-gray-100 transition-colors duration-200"
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
                                                v-if="teamLogoUrl(team.name)"
                                                :src="teamLogoUrl(team.name)"
                                                :alt="team.name"
                                                width="36"
                                                height="36"
                                                class="shrink-0 rounded object-cover"
                                                loading="lazy"
                                            >
                                            <span>{{ team.name }}</span>
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-3.5 text-right text-sm font-semibold tabular-nums text-blue-400">{{ team.power }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- CTA Button -->
                    <button
                        class="p-2 rounded-sm border border-white bg-white text-blue-800 hover:bg-transparent hover:text-white font-semibold text-sm transition-all duration-200 cursor-pointer"
                    >
                        Generate Fixtures
                    </button>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
