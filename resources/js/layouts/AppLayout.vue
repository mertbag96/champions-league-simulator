<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';
import Bg from '../../assets/images/bg.png';
import Logo from '../../assets/images/logo.webp';
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
                <ul class="flex items-center space-x-6">
                    <!-- Manage Teams-->
                    <Link
                        :href="home.url()"
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
</template>
