<script setup lang="ts">
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import { useToast } from '@/composables/useToast'
import AppLayout from '@/layouts/AppLayout.vue';
import { index, update } from '@/routes/teams';
import type { Team, TeamEditForm } from '@/types/team';

const page = usePage();

const toast = useToast();

const props = defineProps<{
  team: Team,
}>()

const form = useForm<TeamEditForm>({
  name: props.team.name,
  power: props.team.power,
  active: props.team.active
});

const submit = () => {
  form.put(update.url({ team: props.team.id }));
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
                <h1 class="font-semibold text-lg md:text-2xl">Edit Team</h1>

                <!-- All Teams -->
                <Link
                    :href="index.url()"
                    class="p-2 rounded-sm border border-white bg-white text-blue-800 hover:bg-transparent hover:text-white font-semibold text-sm transition-all duration-200 cursor-pointer"
                >
                    <font-awesome-icon
                        icon="fa-solid fa-circle-left"
                    />
                    Back to Teams
                </Link>
            </div>

            <!-- Form Container -->
            <div class="w-full xl:w-3/4 2xl:w-1/2 rounded-lg border border-blue-800/75 bg-blue-800/25 shadow-xl backdrop-blur-sm">
                <!-- Form -->
                <form
                    @submit.prevent="submit"
                    class="w-full flex flex-col space-y-8 p-6 md:p-12 text-xs sm:text-sm md:text-md"
                    autocomplete="off"
                >
                    <!-- Name -->
                    <div class="flex flex-col space-y-2">
                        <!-- Label -->
                        <label for="name">
                            Team Name
                            <small class="font-semibold text-xs text-red-400">*</small>
                        </label>

                        <!-- Input -->
                        <input
                            v-model="form.name"
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Please enter a team name"
                            class="py-4 border-b border-b-blue-400 focus:outline-none"
                        />

                        <!-- Error -->
                        <small
                            v-if="form.errors.name"
                            class="font-medium text-xs text-red-400"
                        >
                            {{ form.errors.name }}
                        </small>
                    </div>

                    <!-- Strength -->
                    <div class="flex flex-col space-y-2">
                        <!-- Label -->
                        <label for="power">
                            Team Strength
                            <small class="font-semibold text-xs text-red-400">*</small>
                        </label>

                        <!-- Input -->
                        <input
                            v-model="form.power"
                            type="text"
                            id="power"
                            name="power"
                            placeholder="Please enter a team strength between 0 - 100"
                            class="py-4 border-b border-b-blue-400 focus:outline-none"
                        />

                        <!-- Error -->
                        <small
                            v-if="form.errors.power"
                            class="font-medium text-xs text-red-400"
                        >
                            {{ form.errors.power }}
                        </small>
                    </div>

                    <!-- Active -->
                    <div class="flex flex-col space-y-2">
                        <!-- Label -->
                        <label for="active">
                            Is Team Active?
                            <small class="font-semibold text-xs text-red-400">*</small>
                        </label>

                        <!-- Input -->
                        <select
                            v-model="form.active"
                            id="active"
                            name="active"
                            class="py-4 border-b border-b-blue-400 focus:outline-none"
                        >
                            <option :value="null">Please select an option</option>
                            <option :value=true>Yes</option>
                            <option :value=false>No</option>
                        </select>

                        <!-- Error -->
                        <small
                            v-if="form.errors.active"
                            class="font-medium text-xs text-red-400"
                        >
                            {{ form.errors.active }}
                        </small>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-start">
                        <button
                            type="submit"
                            class="p-2 rounded-sm border border-white bg-white text-blue-800 hover:bg-transparent hover:text-white font-semibold transition-all duration-200 cursor-pointer"
                        >
                            Update Team
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </AppLayout>
</template>
