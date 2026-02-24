<script setup lang="ts">
import { router, usePage, Link } from '@inertiajs/vue3';
import { ref, watch, watchEffect } from 'vue';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { getTeamLogoUrl } from '@/lib/teamLogo';
import { index as fixturesIndex } from '@/routes/fixtures';
import { index, simulate, reset } from '@/routes/simulation';
import type { FixtureWithTeams } from '@/types/fixture';
import type { TeamForTable } from '@/types/team';

const page = usePage();

const toast = useToast();

const props = defineProps<{
  week: number,
  weeks: number[],
  teams: TeamForTable[],
  matches: FixtureWithTeams[]
}>()

const showHint = ref(false);

const selectedWeek = ref(props.week);

const lastWeek = Math.max(...props.weeks);

const isAutoPlaying = ref(false);

const autoPlayDelay = 1000;

const toggleHint = () => {
    showHint.value = ! showHint.value;
};

const autoPlay = async () => {
  if (isAutoPlaying.value) return;

  isAutoPlaying.value = true;

  let currentWeek = selectedWeek.value;

  while (currentWeek <= lastWeek && isAutoPlaying.value) {

    await new Promise((resolve) => {
      router.post(simulate.url({ week: currentWeek }), {}, {
          preserveScroll: true,
          onSuccess: () => {
            selectedWeek.value = currentWeek;
            resolve(true);
          },
        }
      );
    });

    await new Promise((r) => setTimeout(r, autoPlayDelay));

    currentWeek++;
  }

  isAutoPlaying.value = false;
};

watch(() => props.week, (newWeek) => {
    selectedWeek.value = newWeek;
  }
);

watch(selectedWeek, (week) => {
    router.get(index.url({ week }), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
});

watchEffect(() => {
  const success = page.props.flash?.success;
  const error = page.props.flash?.error;

  if (success) {
    toast.success(success);
  }

  if (error) {
    toast.error(error);
  }
});
</script>

<template>
    <AppLayout>
        <main class="w-[90%] xs:container mx-auto py-6 sm:py-12">
            <!-- Week Container -->
            <div class="px-2 md:px-4 space-y-4 lg:space-y-8">
                <!-- Week Matches -->
                <div class="flex flex-col space-y-4">
                    <!-- Navigation -->
                    <div class="w-full flex justify-between items-center space-x-4">
                        <!-- Title -->
                        <h1 class="w-full flex items-center space-x-4 md:w-auto font-semibold text-lg md:text-2xl">
                            <span>Week {{ selectedWeek }}</span>
                            <font-awesome-icon
                                @click="toggleHint"
                                icon="fa-solid fa-circle-info"
                                class="cursor-pointer"
                            />
                        </h1>

                        <!-- Week Selection -->
                        <select
                            v-model="selectedWeek"
                            class="px-6 py-3 bg-white rounded-lg font-semibold text-xs sm:text-sm md:text-md text-blue-800"
                            :disabled="isAutoPlaying"
                        >
                            <option
                                v-for="currentWeek in props.weeks"
                                :key="currentWeek"
                                :value="currentWeek"
                            >
                                Week {{ currentWeek }}
                            </option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="w-full grid grid-cols-2 md:grid-cols-4 gap-4 font-semibold text-xs sm:text-sm md:text-md text-center text-white">
                        <!-- Fixtures -->
                        <Link
                            :href="fixturesIndex.url()"
                            class="bg-white p-2 rounded-sm hover:bg-gray-200 text-blue-800 transition-colors cursor-pointer disabled:opacity-25"
                            :disabled="isAutoPlaying"
                        >
                            <font-awesome-icon icon="fa-solid fa-circle-left" />
                            Back to Fixtures
                        </Link>

                        <!-- Simulate All Weeks -->
                        <button
                            @click="autoPlay"
                            class="bg-green-700 p-2 rounded-sm hover:bg-green-800 transition-colors cursor-pointer"
                        >
                            <font-awesome-icon icon="fa-solid fa-forward" />
                            Simulate All Weeks
                        </button>

                        <!-- Simulate Week -->
                        <Link
                            :href="simulate.url({ week: props.week })"
                            method="post"
                            as="button"
                            class="bg-blue-600 p-2 rounded-sm hover:bg-blue-700 transition-colors cursor-pointer"
                        >
                            <font-awesome-icon icon="fa-solid fa-play" />
                            Simulate Week
                        </Link>

                        <!-- Reset Simulation -->
                        <Link
                            :href="reset.url()"
                            method="delete"
                            as="button"
                            class="bg-red-700 p-2 rounded-sm hover:bg-red-800 transition-colors cursor-pointer"
                        >
                            <font-awesome-icon icon="fa-solid fa-rotate" />
                            Reset Simulation
                        </Link>
                    </div>

                    <!-- Mathces -->
                    <div
                        v-for="match in matches"
                        :key="match.id"
                        class="p-2 md:p-4 bg-blue-950/90 rounded-lg text-xs sm:text-sm md:text-md"
                    >
                        <!-- Match -->
                        <div class="flex justify-between items-center">
                            <!-- Home Team -->
                            <div class="flex flex-col space-y-4">
                                <!-- Home -->
                                <div class="ps-2 flex items-center space-x-1 font-medium text-gray-400">
                                    <font-awesome-icon icon="fa-solid fa-home" />
                                    <span>Home</span>
                                </div>

                                <!-- Team Details -->
                                <div class="flex items-center">
                                    <img
                                        :src="getTeamLogoUrl(match.home_team.name)"
                                        :alt="match.home_team.name"
                                        width="36"
                                        height="36"
                                        class="shrink-0 rounded object-cover"
                                        loading="lazy"
                                        @error="($event.target as HTMLImageElement).style.display = 'none'"
                                    >
                                    <span>{{ match.home_team.name }}</span>
                                </div>
                            </div>

                            <!-- Match Details -->
                            <div class="text-center px-4">
                                <!-- Score -->
                                <div class="text-xl font-bold">
                                    {{ match.played ? `${match.home_score} - ${match.away_score}` : '-' }}
                                </div>

                                <!-- Status -->
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ match.played ? 'Played' : 'Upcoming' }}
                                </div>
                            </div>

                            <!-- Away Team -->
                            <div class="flex flex-col space-y-4 text-right">
                                <!-- Away -->
                                <div class="pe-2 flex justify-end items-center space-x-1 font-medium text-gray-400">
                                    <span>Away</span>
                                    <font-awesome-icon icon="fa-solid fa-plane-departure" />
                                </div>

                                <!-- Team Details -->
                                <div class="flex items-center">
                                    <span>{{ match.away_team.name }}</span>
                                    <img
                                        :src="getTeamLogoUrl(match.away_team.name)"
                                        :alt="match.away_team.name"
                                        width="36"
                                        height="36"
                                        class="shrink-0 rounded object-cover"
                                        loading="lazy"
                                        @error="($event.target as HTMLImageElement).style.display = 'none'"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="flex flex-col space-y-4">
                    <!-- Title -->
                    <h1 class="w-full md:w-auto font-semibold text-lg md:text-2xl">Table</h1>

                    <!-- Table Container -->
                    <div class="overflow-auto w-full rounded-lg border border-blue-800/75 bg-blue-800/25 shadow-xl backdrop-blur-sm">
                        <!-- Table -->
                        <table class="w-full border-collapse text-left overflow-auto">
                            <!-- Head -->
                            <thead class="font-semibold text-gray-200">
                                <tr class="border-b border-blue-800 bg-blue-800">
                                    <th class="min-w-[40px] px-4 sm:px-6 py-4 text-sm">Pos</th>
                                    <th class="min-w-[240px] px-4 sm:px-6 py-4 text-sm">Team</th>
                                    <th class="min-w-[50px] px-4 sm:px-6 py-4 text-sm">Pl</th>
                                    <th class="min-w-[50px] px-4 sm:px-6 py-4 text-sm">W</th>
                                    <th class="min-w-[50px] px-4 sm:px-6 py-4 text-sm">D</th>
                                    <th class="min-w-[50px] px-4 sm:px-6 py-4 text-sm">L</th>
                                    <th class="min-w-[50px] px-4 sm:px-6 py-4 text-sm">GF</th>
                                    <th class="min-w-[50px] px-4 sm:px-6 py-4 text-sm">GA</th>
                                    <th class="min-w-[50px] px-4 sm:px-6 py-4 text-sm">GD</th>
                                    <th class="min-w-[50px] px-4 sm:px-6 py-4 text-sm">Pts</th>
                                    <th
                                        v-if="selectedWeek !== lastWeek"
                                        class="min-w-[50px] px-4 sm:px-6 py-4 text-sm"
                                    >
                                        Next
                                    </th>
                                </tr>
                            </thead>

                            <!-- Body -->
                            <tbody class="divide-y divide-blue-600/50 font-medium text-white">
                                <tr
                                    v-for="(team, order) in props.teams"
                                    :key="order"
                                    class="bg-gray-800/20 hover:bg-blue-900/20 transition-colors duration-150"
                                >
                                    <td class="px-4 sm:px-6 py-3.5 text-sm">{{ order + 1}}</td>
                                    <td class="px-4 sm:px-6 py-3.5 text-sm flex items-center">
                                        <img
                                            :src="getTeamLogoUrl(team.name)"
                                            :alt="team.name"
                                            width="36"
                                            height="36"
                                            class="shrink-0 rounded object-cover"
                                            loading="lazy"
                                            @error="($event.target as HTMLImageElement).style.display = 'none'"
                                        >
                                        {{ team.name }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.played }}</td>
                                    <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.wins }}</td>
                                    <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.draws }}</td>
                                    <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.losses }}</td>
                                    <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.goals_for }}</td>
                                    <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.goals_against }}</td>
                                    <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.goal_difference }}</td>
                                    <td class="px-4 sm:px-6 py-3.5 text-sm">{{ team.points }}</td>
                                    <td
                                        v-if="props.week !== lastWeek"
                                        class="px-4 sm:px-6 py-3.5 text-sm"
                                    >
                                        <img
                                            :src="getTeamLogoUrl(team.next_opponent)"
                                            :alt="team.next_opponent"
                                            width="36"
                                            height="36"
                                            class="shrink-0 rounded object-cover"
                                            loading="lazy"
                                            @error="($event.target as HTMLImageElement).style.display = 'none'"
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Note -->
                    <p class="block md:hidden italic text-center text-xs sm:text-sm">
                        Please scroll right to see the rest of the table
                    </p>
                </div>
            </div>
        </main>
    </AppLayout>

    <!-- Hint -->
    <div
        v-if="showHint"
        @click.self="toggleHint"
        class="fixed inset-0 bg-blue-950/50 flex items-center justify-center z-50 backdrop-blur-xs p-4"
    >
        <div class="overflow-auto bg-blue-950 border border-blue-700/50 rounded-lg shadow-lg max-w-4xl w-full text-xs sm:text-sm md:text-md text-white">
            <!-- Header -->
            <div class="px-4 md:px-6 py-2.5 md:py-4 border-b border-blue-700/50 flex justify-between items-center">
                <h3 class="font-semibold">Information about Simulation</h3>

                <button
                    @click="toggleHint"
                    lass="text-lg cursor-pointer text-white"
                >
                    <font-awesome-icon icon="fa-solid fa-circle-xmark" />
                </button>
            </div>

            <!-- Body -->
            <div class="bg-blue-900/50 px-4 md:px-6 py-2.5 md:py-4 space-y-4">
                <p class="text-xs sm:text-sm md:text-md">
                    Welcome to the <strong>Champions League (UCL) Simulation</strong>. <br><br>
                    - You can simulate all weeks automatically. <br>
                    - You can simulate week by week by selecting each week from the dropdown. <br>
                    - You can go back to fixtures to see all matches at once. <br>
                    - You can delete all the simulation data by clicking "Reset Simulation" button.<br><br>
                    Hope you enjoy it
                    <font-awesome-icon icon="fa-solid fa-smile" />
                </p>
            </div>
        </div>
    </div>

    <!-- Simulation Loader -->
    <div
        v-if="isAutoPlaying"
        class="fixed inset-0 bg-blue-950/50 flex items-center justify-center z-50 backdrop-blur-xs p-4"
    >
        <div responsive-height-comments>
            <div class="loader">
                <svg viewBox="0 0 866 866" xmlns="http://www.w3.org/2000/svg">
                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 164.83 151.5">
                        <path class="path-0" d="M117.24,69.24A8,8,0,0,0,115.67,67c-4.88-4-9.8-7.89-14.86-11.62A4.93,4.93,0,0,0,96.93,55c-5.76,1.89-11.4,4.17-17.18,6a4.36,4.36,0,0,0-3.42,4.12c-1,6.89-2.1,13.76-3,20.66a4,4,0,0,0,1,3.07c5.12,4.36,10.39,8.61,15.68,12.76a3.62,3.62,0,0,0,2.92.75c6.29-2.66,12.52-5.47,18.71-8.36a3.49,3.49,0,0,0,1.68-2.19c1.34-7.25,2.54-14.55,3.9-22.58Z"
                            fill="#ffffff" />
                        <path class="path-1" d="M97.55,38.68A43.76,43.76,0,0,1,98,33.44c.41-2.36-.5-3.57-2.57-4.64C91.1,26.59,87,24,82.66,21.82a6.18,6.18,0,0,0-4-.71C73.45,22.55,68.32,24.25,63.22,26c-3.63,1.21-6.08,3.35-5.76,7.69a26.67,26.67,0,0,1-.6,4.92c-1.08,8.06-1.08,8.08,5.86,11.92,3.95,2.19,7.82,5.75,11.94,6.08s8.76-2.41,13.12-3.93c9.33-3.29,9.33-3.3,9.78-14Z"
                            fill="#ffffff" />
                        <path class="path-2" d="M66.11,126.56c5.91-.91,11.37-1.7,16.81-2.71a3.3,3.3,0,0,0,1.87-2.17c1-4.06,1.73-8.19,2.84-12.24.54-2-.11-3-1.55-4.15-5-4-9.9-8.12-15-12a6.19,6.19,0,0,0-4.15-1.1c-5.35.66-10.7,1.54-16,2.54A4,4,0,0,0,48.34,97a109.13,109.13,0,0,0-3,12.19,4.47,4.47,0,0,0,1.34,3.6c5.54,4.36,11.23,8.53,16.91,12.69a10.84,10.84,0,0,0,2.57,1.11Z"
                            fill="#ffffff" />
                        <path class="path-3" d="M127.42,104.12c4.1-2.1,8-3.93,11.72-6a6,6,0,0,0,2.27-3,58.22,58.22,0,0,0,3.18-29.92c-.26-1.7-8-7.28-9.71-6.85A5,5,0,0,0,133,59.65c-2.81,2.49-5.71,4.88-8.33,7.56a9.46,9.46,0,0,0-2.47,4.4c-1.29,6.49-2.38,13-3.35,19.55a5.73,5.73,0,0,0,.83,3.91c2.31,3.08,5,5.88,7.7,9Z"
                            fill="#ffffff" />
                        <path class="path-4" d="M52.58,29.89c-2.15-.36-3.78-.54-5.39-.9-2.83-.64-4.92.1-7,2.32A64.1,64.1,0,0,0,26.09,54.64c-2.64,7.92-2.62,7.84,5.15,10.87,1.76.69,2.73.45,3.93-1C39.79,59,44.54,53.65,49.22,48.2a4.2,4.2,0,0,0,1.13-2c.8-5.32,1.49-10.68,2.24-16.34Z"
                            fill="#ffffff" />
                        <path class="path-5" fill="#ffffff" d="M23,68.13c0,2.51,0,4.7,0,6.87a60.49,60.49,0,0,0,9.75,32.15c1.37,2.13,6.4,3,7,1.2,1.55-5,2.68-10.2,3.82-15.34.13-.58-.58-1.38-.94-2.06-2.51-4.77-5.47-9.38-7.45-14.37C32.94,71,28.22,69.84,23,68.13Z" />
                        <path class="path-6" fill="#ffffff" d="M83.91,12.86c-.32.36-.66.71-1,1.07.9,1.13,1.57,2.62,2.73,3.33,4.71,2.84,9.56,5.48,14.39,8.1a9.29,9.29,0,0,0,3.13.83c5.45.69,10.89,1.38,16.35,1.94a10.41,10.41,0,0,0,3.07-.71c-11.48-9.9-24.26-14.61-38.71-14.56Z"
                        />
                        <path class="path-7" fill="#ffffff" d="M66.28,132.51c13.36,3.78,25.62,3.5,38-.9C91.68,129.59,79.36,128,66.28,132.51Z" />
                        <path class="path-8" fill="#ffffff" d="M127.2,30.66l-1.27.37a18.58,18.58,0,0,0,1,3.08c3,5.52,6.21,10.89,8.89,16.54,1.34,2.83,3.41,3.82,6.49,4.9a60.38,60.38,0,0,0-15.12-24.9Z" />
                        <path class="bb-9" fill="#ffffff" d="M117.35,125c5.58-2.32,16.9-13.84,18.1-19.2-2.41,1.46-5.18,2.36-6.78,4.23-4.21,5-7.89,10.37-11.32,15Z" />
                    </svg>
                </svg>
            </div>
        </div>
    </div>
</template>
