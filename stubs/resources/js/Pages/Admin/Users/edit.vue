<template>
    <div>
        <div>
            <a href="/admin/users" class="btn-back">
                <i class="mdi mdi-menu-left"></i>
            </a>
            <h1>{{ form.email }}</h1>
        </div>

        <div class="info">
            <h3>User Information</h3>
            <p>Update User's profile information and email address.</p>
        </div>

        <div class="card">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <input type="text" v-model="form.first_name" placeholder="First name" required>
                    <input type="text" v-model="form.last_name" placeholder="Last name" required>
                    <input type="text" v-model="form.username" placeholder="Username" required>
                    <input type="email" v-model="form.email" placeholder="Email" required>
                    <input type="text" v-model="form.phone" placeholder="Phone" required>
                    <input type="text" v-model="form.address" placeholder="Address" required>
                    <input type="text" v-model="form.city" placeholder="City" required>
                    <input type="text" v-model="form.state" placeholder="State" required>
                    <input type="text" v-model="form.zip" placeholder="Zip" required>
                    <button type="submit">Save</button>
                </form>
            </div>
        </div>

        <div class="info">
            <h3>Roles</h3>
            <p>Roles are used to determine what permissions a user has.</p>
        </div>

        <div class="card">
            <div class="card-body">
                <select v-model="form.roles" multiple>
                    <option v-for="role in roles" :value="role.id">{{ role.name }}</option>
                </select>
                <button @click="submit">Save</button>
            </div>
        </div>
    </div>
</template>

<script>
// import Layout from '@/Layouts/Admin/Layout';

export default {
    // props: ['user', 'roles'],
    // data() {
    //     return {
    //         form: this.$inertia.form({ ...this.user.data })
    //     };
    // },
    // methods: {
    //     submit() {
    //         this.form
    //             .transform((data) => {
    //                 if (typeof data.roles === 'string') {
    //                     data.roles = [data.roles];
    //                 }
    //                 return data;
    //             })
    //             .put(this.route('admin.users.update', this.form), {
    //                 onSuccess: (data) => {
    //                     // console.log(data);
    //                 }
    //             });
    //     }
    // },

    // Layout,

};
</script>

<style scoped></style>
