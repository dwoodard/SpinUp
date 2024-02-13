<template>
    <form @submit.prevent="submit">
        <div>
            <input v-model="form.email" :class="{ 'error': form.errors.email }" type="email" placeholder="Email" required>
            <input v-model="form.username" :class="{ 'error': form.errors.username }" type="text" placeholder="Username" required>
            <input v-model="form.password" :class="{ 'error': form.errors.password }" type="password" placeholder="Password" required>
            <select v-model="form.role" :class="{ 'error': form.errors.role }">
                <option v-for="role in roles" :key="role.name" :value="role.name">{{ role.name }}</option>
            </select>
        </div>


        <div class="card-actions">
            <button class="btn" @click.stop="show = false">Close</button>

            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </form>
</template>

<script>

export default {
    props: {
        value: Boolean
    },
    data() {
        return {
            hidepassword: true,
            roles: [{ name: 'admin' }, { name: 'user' }],
            form: this.$inertia.form({
                username: '',
                email: '',
                password: '',
                role: 'user'
            })
        };
    },
    computed: {
        show: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit('input', value);
            }
        }

    },
    methods: {
        submit() {
            this.form.post(route('admin.users.store'), {
                onSuccess: (data) => {
                    this.form.reset();
                    this.show = false;
                },
                onFinish: () => { }
            });
        }
    }

};
</script>
