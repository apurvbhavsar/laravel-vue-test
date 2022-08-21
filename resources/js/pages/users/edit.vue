<template>
    <div class="row">
        <div class="col-lg-8 m-auto">
            <b-card header="Update my profile">
                <b-form
                    @submit.prevent="update"
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

                    <b-button type="submit" variant="primary">Save</b-button>
                </b-form>
            </b-card>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Form from "vform";

export default {
    components: {},

    middleware: "auth",

    data: () => ({
        form: new Form({
            id: "",
            name: "",
            email: "",
        }),
    }),

    methods: {
        async loadUser() {
            const { data } = await axios.get("/api/me");
            this.form.name = data.data.name;
            this.form.email = data.data.email;
        },
        async update() {
            const { data } = await this.form.post("/api/me");
            this.$router.push({ name: "posts" });
        },
    },
    mounted() {
        this.loadUser();
    },
};
</script>
