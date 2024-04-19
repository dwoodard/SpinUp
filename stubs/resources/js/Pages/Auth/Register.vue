<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Register" />

    <form @submit.prevent="submit">
      <Input
        label="First Name"
        v-model="form.first_name"
        :error="form.errors.first_name"
        required
        autofocus
      />

      <Input
        label="Last Name"
        v-model="form.last_name"
        :error="form.errors.last_name"
        required
        autofocus
      />

      <Input
        label="Email"
        type="email"
        v-model="form.email"
        :error="form.errors.email"
        required
        autocomplete="email"
      />

      <Input
        label="Password"
        type="password"
        v-model="form.password"
        :error="form.errors.password"
        required
        autocomplete="new-password"
      />

      <Input
        label="Confirm Password"
        type="password"
        v-model="form.password_confirmation"
        :error="form.errors.password_confirmation"
        required
        autocomplete="new-password"
      />

      <div class="flex items-center justify-end mt-4">
        <Link
          :href="route('login')"
          class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
        >
          Already registered?
        </Link>

        <Button
          class="ms-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Register
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
