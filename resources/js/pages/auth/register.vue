<template>
    <div class="row">
        <div class="col-lg-8 m-auto">
            <b-card header="Create Account">
                <b-form
                    @submit.prevent="register"
                    @keydown="form.onKeydown($event)"
                >
                    <b-form-group label="Name:" label-for="name">
                        <b-form-input
                            type="text"
                            id="name"
                            name="name"
                            :class="{
                                'is-invalid': form.errors.has('name'),
                            }"
                            v-model="form.name"
                        ></b-form-input>
                        <has-error :form="form" field="name" />
                    </b-form-group>

                    <b-form-group label="Email:" label-for="email">
                        <b-form-input
                            type="email"
                            id="email"
                            name="email"
                            :class="{ 'is-invalid': form.errors.has('email') }"
                            v-model="form.email"
                        ></b-form-input>
                        <has-error :form="form" field="email" />
                    </b-form-group>

                    <b-form-group label="Password:" label-for="password">
                        <b-form-input
                            type="password"
                            id="password"
                            name="password"
                            :class="{
                                'is-invalid': form.errors.has('password'),
                            }"
                            v-model="form.password"
                        ></b-form-input>
                        <has-error :form="form" field="password" />
                    </b-form-group>

                    <b-form-group
                        label="Confirm Password:"
                        label-for="password"
                    >
                        <b-form-input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            :class="{
                                'is-invalid': form.errors.has(
                                    'password_confirmation'
                                ),
                            }"
                            v-model="form.password_confirmation"
                        ></b-form-input>
                        <has-error :form="form" field="password_confirmation" />
                    </b-form-group>
                    <b-button type="submit" variant="primary">Create</b-button>

                    <b-link to="/login">Login</b-link>
                </b-form>
            </b-card>
        </div>
    </div>
</template>

<script>
import Form from "vform";

export default {
    components: {},

    middleware: "guest",

    data: () => ({
        form: new Form({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        }),
        remember: false,
    }),

    methods: {
        async register() {
            const { data } = await this.form.post("/api/register");

            this.$store.dispatch("auth/saveToken", {
                token: data.token,
                remember: this.remember,
            });

            await this.$store.dispatch("auth/fetchUser");

            this.redirect();
        },

        redirect() {
            this.$router.push({ name: "posts" });
        },
    },
};
</script>
