<template>
    <Layout>


    <!-- users table -->
    <div class="card">
      <div class="card-body">
        <table>
          <thead>
            <tr>
              <th>username</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users.data" :key="index">
              <td>{{ user.username }}</td>
              <td>{{ user.first_name }}</td>
              <td>{{ user.last_name }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.role }}</td>
              <td>
                <button>Edit</button>
                <button>Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Admin/Layout.vue'
import { Table } from '@/Components/ui/table'

import { defineProps } from 'vue'

defineProps({
  users: {
    type: Object,
  },
})
</script>

<style scoped></style>
