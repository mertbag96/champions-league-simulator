<script setup lang="ts">
import { usePage, Link } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { getTeamLogoUrl } from '@/lib/teamLogo';
import { store, reset } from '@/routes/fixtures';
import { index } from '@/routes/simulation';

const page = usePage();

const toast = useToast();

const props = defineProps<{
  teamsCount: number
  totalWeeks: number
  fixturesCount: number
  fixturesByWeek: Record<number, any[]>
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
        <main class="w-[90%] xs:container mx-auto py-6 sm:py-12 space-y-8">
            <!-- Fixtures Container -->
            <div
                v-if="fixturesCount > 0"
                class="px-2 md:px-4 flex flex-col space-y-6 lg:space-y-8"
            >
                <!-- Navigation -->
                <div class="flex flex-wrap justify-between items-center gap-4 md:gap-6 xl:gap-8">
                    <!-- Title -->
                    <h1 class="w-full md:w-auto font-semibold text-lg md:text-2xl">Fixtures</h1>

                    <!-- Summary -->
                    <p class="w-full md:w-auto text-xs sm:text-sm md:text-md lg:text-lg text-blue-100">
                        <font-awesome-icon icon="fa-solid fa-futbol" />
                        {{ fixturesCount }} matches •
                        <font-awesome-icon icon="fa-solid fa-people-group" />
                        {{ teamsCount }} teams •
                        <font-awesome-icon icon="fa-solid fa-calendar-days" />
                        {{ totalWeeks }} weeks
                    </p>

                    <!-- Action Buttons -->
                    <div class="w-full flex flex-wrap justify-between gap-4 mt-2 md:mt-0 font-semibold transition-all duration-200 text-center">
                        <!-- Start Simulation -->
                        <Link
                            :href="index.url(1)"
                            class="w-full md:w-auto p-2 md:p-4 rounded-sm border border-white bg-white text-blue-800 hover:bg-transparent hover:text-white cursor-pointer"
                        >
                            <font-awesome-icon icon="fa-solid fa-circle-play" />
                            Start Simulation
                        </Link>

                        <!-- Reset Simulation -->
                        <Link
                            :href="reset.url()"
                            method="delete"
                            as="button"
                            class="w-full md:w-auto p-2 md:p-4 rounded-sm border border-red-800 bg-red-700 text-white hover:bg-red-600 cursor-pointer"
                        >
                            <font-awesome-icon icon="fa-solid fa-trash-can" />
                            Delete Fixtures
                        </Link>
                    </div>
                </div>

                <!-- Fixtures By Week -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 xl:gap-8">
                    <!-- Week -->
                    <div
                        v-for="(fixtures, week) in props.fixturesByWeek"
                        :key="week"
                        class="p-3 md:p-6 bg-blue-900/75 border border-blue-700 rounded-lg flex flex-col space-y-4"
                    >
                        <!-- Week Title -->
                        <h2 class="font-semibold flex items-center space-x-2 text-md lg:text-xl">
                            <span>Week {{ week }}</span>
                            <span class="text-sm text-gray-400">({{ fixtures.length }} matches)</span>
                        </h2>

                        <!-- Matches -->
                        <div
                            v-for="fixture in fixtures"
                            :key="fixture.id"
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
                                            :src="getTeamLogoUrl(fixture.home_team.name)"
                                            :alt="fixture.home_team.name"
                                            width="36"
                                            height="36"
                                            class="shrink-0 rounded object-cover"
                                            loading="lazy"
                                            @error="($event.target as HTMLImageElement).style.display = 'none'"
                                        >
                                        <span>{{ fixture.home_team.name }}</span>
                                    </div>
                                </div>

                                <!-- Match Details -->
                                <div class="text-center px-4">
                                    <!-- Score -->
                                    <div class="text-sm sm:txt-md lg:text-xl font-bold">
                                        {{ fixture.played ? `${fixture.home_score} - ${fixture.away_score}` : '-' }}
                                    </div>

                                    <!-- Status -->
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ fixture.played ? 'Played' : 'Upcoming' }}
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
                                        <span>{{ fixture.away_team.name }}</span>
                                        <img
                                            :src="getTeamLogoUrl(fixture.away_team.name)"
                                            :alt="fixture.away_team.name"
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
                </div>
            </div>

            <!-- No Fixtures Container -->
            <div
                v-else
                class="px-2 md:px-4 flex flex-col space-y-12"
            >
                <!-- Navigation -->
                <div class="flex flex-wrap justify-between items-center gap-4 md:gap-6 xl:gap-8">
                    <!-- Title -->
                    <h1 class="w-full md:w-auto font-semibold text-lg md:text-2xl">Fixtures</h1>

                    <!-- Summary -->
                    <p class="w-full md:w-auto text-xs sm:text-sm md:text-md lg:text-lg text-blue-100">
                        <font-awesome-icon icon="fa-solid fa-futbol" />
                        {{ fixturesCount }} matches •
                        <font-awesome-icon icon="fa-solid fa-people-group" />
                        {{ teamsCount }} teams •
                        <font-awesome-icon icon="fa-solid fa-calendar-days" />
                        {{ totalWeeks }} weeks
                    </p>
                </div>

                <!-- No Fixtures Found -->
                <div class="w-full h-[120px] lg:h-[240px] flex flex-col justify-center items-center space-y-12">
                    <!-- Description -->
                    <p class="font-medium text-sm md:text-md lg:text-lg text-gray-400 text-center">
                        No fixtures were found! Please generate fixtures to start simulation.
                    </p>

                    <!-- Reset Simulation -->
                    <Link
                        :href="store.url()"
                        method="post"
                        as="button"
                        class="w-full md:w-auto p-2 md:p-4 rounded-sm border border-white bg-white font-semibold text-blue-800 text-center hover:bg-transparent hover:text-white cursor-pointer"
                    >
                        Generate Fixtures
                    </Link>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
