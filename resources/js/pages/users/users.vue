<template>
    <b-card header="Users">
        <b-button v-b-modal.modal-create class="mb-2" variant="primary"
            >Create User</b-button
        >

        <b-modal
            v-model="create_modal_show"
            id="modal-create"
            no-close-on-backdrop
            content-class="shadow"
            title="Create User"
        >
            <b-form
                @submit.prevent="register"
                @keydown="form.onKeydown($event)"
            >
                <b-form-group label="Name:" label-for="name">
                    <b-form-input
                        type="text"
                        id="name"
                        :class="{ 'is-invalid': form.errors.has('name') }"
                        v-model="form.name"
                    >
                    </b-form-input>
                    <has-error :form="form" field="name" />
                </b-form-group>

                <b-form-group label="Email:" label-for="email">
                    <b-form-input
                        type="email"
                        id="email"
                        :class="{ 'is-invalid': form.errors.has('email') }"
                        v-model="form.email"
                    >
                    </b-form-input>
                    <has-error :form="form" field="email" />
                </b-form-group>

                <b-form-group label="Password:" label-for="password">
                    <b-form-input
                        type="password"
                        id="password"
                        :class="{ 'is-invalid': form.errors.has('password') }"
                        v-model="form.password"
                    >
                    </b-form-input>
                    <has-error :form="form" field="password" />
                </b-form-group>
            </b-form>
            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="danger"
                        class="float-right"
                        @click="create_modal_show = false"
                    >
                        Close
                    </b-button>
                    <b-button
                        type="submit"
                        variant="primary"
                        class="float-right mr-2"
                        @click="create_user()"
                    >
                        Save
                    </b-button>
                </div>
            </template>
        </b-modal>
        <b-modal
            v-model="edit_modal_show"
            id="modal-edit"
            no-close-on-backdrop
            content-class="shadow"
            title="Edit User"
        >
            <b-form
                @submit.prevent="register"
                @keydown="editform.onKeydown($event)"
            >
                <b-form-group label="Name:" label-for="edit_name">
                    <b-form-input
                        type="text"
                        id="edit_name"
                        :class="{ 'is-invalid': editform.errors.has('name') }"
                        v-model="editform.name"
                    >
                    </b-form-input>
                    <has-error :form="editform" field="name" />
                </b-form-group>
                <b-form-group label="Email:" label-for="edit_email">
                    <b-form-input
                        type="email"
                        id="edit_email"
                        :class="{ 'is-invalid': editform.errors.has('email') }"
                        v-model="editform.email"
                    >
                    </b-form-input>
                    <has-error :form="editform" field="email" />
                </b-form-group>
            </b-form>
            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="danger"
                        class="float-right"
                        @click="edit_modal_show = false"
                    >
                        Close
                    </b-button>
                    <b-button
                        type="submit"
                        variant="primary"
                        class="float-right mr-2"
                        @click="save_user"
                    >
                        Save
                    </b-button>
                </div>
            </template>
        </b-modal>
        <b-row>
            <b-col lg="6" class="my-1">
                <b-form-group
                label="Filter"
                label-for="filter-input"
                label-cols-sm="3"
                label-align-sm="right"
                label-size="sm"
                class="mb-0"
                >
                <b-input-group size="sm">
                    <b-form-input
                    id="filter-input"
                    v-model="filter"
                    type="search"
                    placeholder="Type to Search"
                    @keyup="filterSearch()"
                    ></b-form-input>

                    <b-input-group-append>
                        <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                    </b-input-group-append>
                </b-input-group>
                </b-form-group>
            </b-col>

            <b-col lg="6" class="my-1">
                <b-form-group
                    label="Per page"
                    label-for="per-page-select"
                    label-cols-sm="6"
                    label-cols-md="4"
                    label-cols-lg="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                <b-form-select
                    id="per-page-select"
                    v-model="perPage"
                    :options="pageOptions"
                    size="sm"
                    @change="loadUsers"
                ></b-form-select>
                </b-form-group>
            </b-col>
        </b-row>
        <b-table
            striped
            hover
            show-empty
            :items="items"
            :fields="fields"
            :current-page="currentPage"
            :per-page="0"
            :filter="filter"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :sort-direction="sortDirection"
        >
            <template #cell(action)="data">
                <b-button
                    size="sm"
                    class="m-2"
                    @click="openModal(data.value)"
                    variant="info"
                >
                    Edit
                </b-button>
                <b-button
                    size="sm"
                    @click="deleteItem(data.value)"
                    variant="danger"
                >
                    Delete
                </b-button>
            </template>
            <template #cell(user)="data">
                {{ data.value.name }}
            </template>
        </b-table>
        <b-pagination
            size="md"
            :total-rows="totalItems"
            v-model="currentPage"
            :per-page="perPage"
            @change="loadUsers"
        ></b-pagination>
        <div>
            Sorting By: <b>{{ sortBy }}</b>, Sort Direction:
            <b>{{ sortDesc ? 'Descending' : 'Ascending' }}</b>
        </div>
    </b-card>
</template>

<script>
import axios from "axios";
import Form from "vform";
import _ from 'lodash';
import toast from "../../mixins/message";

export default {
    middleware: "auth",
    mixins: [toast],
    data() {
        return {
            create_modal_show: false,
            edit_modal_show: false,
            sortBy: 'name',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            fields: [
                { key: 'name', label: 'Name', sortable: true, sortDirection: 'desc' },
                { key: 'email', label: 'User', sortable: true },
                { key: 'action', label: 'Action' }
            ],
            items: [],
            currentPage: 1,
            perPage: 10,
            totalItems: 0,
            authors: [],
            form: new Form({
                name: "",
                email: "",
                password: "",
            }),
            editform: new Form({
                id: 0,
                name: "",
                email: "",
            }),
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
        };
    },
    computed: {
      sortOptions() {
        // Create an options list from our fields
        return this.fields
          .filter(f => f.sortable)
          .map(f => {
            return { text: f.label, value: f.key }
          })
      }
    },
    methods: {
        filterSearch() {
            _.debounce(this.loadUsers(), 1000);
        },
        async loadUsers() {
            const { data } = await axios.get(`/api/users?page=${this.currentPage}&per_page=${this.perPage}&sort=${this.sortBy}&direction=${this.sortDirection}&search=${this.filter}`);
            this.items = data.data;
            this.totalItems = data.meta.total;
        },
        openModal(item_id) {
            let item = this.items.find(({ id }) => id === item_id);
            this.edit_modal_show = true;
            this.editform.id = item.id;
            this.editform.name = item.name;
            this.editform.email = item.email;
        },
        async create_user() {
            const { data } = await this.form.post("/api/users");
            if (data.data.id > 0) {
                this.$show_message(
                    `Notification`,
                    "The user was created successfully",
                    "success"
                );
                this.create_modal_show = false;
                this.loadUsers();
                this.form.name = "";
                this.form.email = "";
                this.form.password = "";
            } else {
                this.$show_message(
                    `Notification`,
                    "User creating operation was failed",
                    "danger"
                );
            }
        },
        async save_user() {
            const { data } = await this.editform.post(
                "/api/users/" + this.editform.id
            );
            if (data.data.id > 0) {
                this.$show_message(
                    `Notification`,
                    "The user was updated successfully",
                    "success"
                );
                this.edit_modal_show = false;
                this.loadUsers();
            } else {
                this.$show_message(
                    `Notification`,
                    "User updating operation was failed",
                    "danger"
                );
            }
        },
        async deleteItem(id) {
            const { data } = await this.editform.delete("/api/users/" + id);
            if (data.status === "success") {
                this.$show_message(
                    `Notification`,
                    "The user was deleted successfully",
                    "success"
                );
            } else {
                this.$show_message(
                    `Notification`,
                    "User deleting operation was failed",
                    "danger"
                );
            }
            this.loadUsers();
        },
    },
    mounted() {
        this.loadUsers();
    },
};
</script>
