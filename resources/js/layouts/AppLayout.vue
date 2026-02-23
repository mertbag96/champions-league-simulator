<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { getTeamLogoUrl } from '@/lib/teamLogo';
import { home } from '@/routes';
import { index } from '@/routes/teams';
import Bg from '../../assets/images/bg.png';
import Logo from '../../assets/images/logo.webp';

const availableTeams = [
    'Arsenal',
    'Aston Villa',
    'Atletico Madrid',
    'Barcelona',
    'Bayer Leverkusen',
    'Bayern Munchen',
    'Benfica',
    'Beşiktaş',
    'BVB',
    'Chelsea',
    'Everton',
    'Fenerbahçe',
    'Galatasaray',
    'Inter Milan',
    'Juventus',
    'Liverpool',
    'Manchester City',
    'Manchester United',
    'Olympique Lyonnais',
    'Porto',
    'PSG',
    'Real Madrid',
    'Shakhtar Donetsk',
    'Tottenham',
];

const showInformationModal = ref(false);

const toggleInformationModal = () => {
  showInformationModal.value = ! showInformationModal.value;
}
</script>

<template>
    <!-- Layout -->
    <div
        class="w-full min-h-dvh py-4 bg-cover bg-center bg-no-repeat"
        :style="{ backgroundImage: `url(${Bg})` }"
    >
        <!-- Header -->
        <header class="w-[90%] xs:container mx-auto px-2 md:px-4 py-2 sm:py-4">
            <div class="flex justify-between">
                <!-- Logo -->
                <Link
                    :href="home.url()"
                    class="flex items-center space-x-2"
                >
                    <!-- Image -->
                    <img
                        :src="Logo"
                        class="w-6 md:w-8 rounded-lg"
                        alt="UCL Simulator"
                    >
                    <!-- Label -->
                    <span class="font-medium text-sm md:text-lg">
                        UCL Simulator
                    </span>
                </Link>

                <!-- Menu -->
                <ul class="flex items-center space-x-4 sm:space-x-6">
                    <!-- Information -->
                    <button
                        @click="toggleInformationModal"
                        class="text-2xl text-white cursor-pointer"
                    >
                        <font-awesome-icon icon="fa-solid fa-circle-question" />
                    </button>
                    <!-- Manage Teams-->
                    <Link
                        :href="index.url()"
                        class="p-2 rounded-sm border border-white bg-white text-blue-800 hover:bg-transparent hover:text-white font-semibold text-sm transition-all duration-200"
                    >
                        Manage Teams
                    </Link>
                </ul>
            </div>
        </header>

        <!-- Content -->
        <main class="content flex">
            <slot />
        </main>
    </div>

    <!-- Information Modal -->
    <div
        v-if="showInformationModal"
        class="fixed inset-0 bg-blue/70 flex items-center justify-center z-50 backdrop-blur-xs p-4"
        @click.self="toggleInformationModal"
    >
        <div class="overflow-auto bg-blue-950 border border-blue-700/50 rounded-lg shadow-lg max-w-4xl w-full text-xs sm:text-sm md:text-md text-white">
            <!-- Header -->
            <div class="px-4 md:px-6 py-2.5 md:py-4 border-b border-blue-700/50 flex justify-between items-center">
                <h3 class="font-semibold">Information About Team Logos</h3>

                <button @click="toggleInformationModal" class="text-lg cursor-pointer text-white">
                    <font-awesome-icon icon="fa-solid fa-circle-xmark" />
                </button>
            </div>

            <!-- Body -->
            <div class="bg-blue-900/50 px-4 md:px-6 py-2.5 md:py-4 space-y-4">
                <p>Currently, only a few teams have logos in the app. The teams that have logos are listed below. This information can be useful when creating or editing teams.</p>

                <p class="text-gray-400 italic">In order to display logos correctly, please name your teams exactly as shown below.</p>

                <div class="pt-4 border-t border-t-blue-900 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div
                        v-for="name in availableTeams"
                        :key="name"
                        class="flex items-center"
                    >
                        <img
                            :src="getTeamLogoUrl(name)"
                            :alt="name"
                            width="36"
                            height="36"
                            class="shrink-0 rounded object-cover"
                            loading="lazy"
                            @error="($event.target as HTMLImageElement).style.display = 'none'"
                        >
                        <span>{{ name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toaster -->
    <Toaster
        richColors
        position="top-center"
        :expand="true"
    />
</template>
