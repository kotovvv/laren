<template>
  <v-card class="mx-auto" max-width="500">
    <v-data-table
      :headers="headers"
      :items="providers"
      sort-by="role_id"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Suppliers</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>

          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                Add a supplier
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">
                  Cancel
                </v-btn>
                <v-btn color="blue darken-1" text @click="save"> Save </v-btn>
              </v-card-actions>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="6">
                      <v-text-field
                        v-model="editedItem.name"
                        label="Name"
                      ></v-text-field>
                    </v-col>
                    <v-text-field
                      v-model="editedItem.password"
                      label="Password"
                    ></v-text-field>

                    <v-col cols="12">
                      <v-select
                        multiple
                        :items="users"
                        v-model="editedItem.related_users_id"
                        item-text="name"
                        item-value="id"
                        label="Related users"
                      ></v-select>
                    </v-col>
                    <v-col cols="12">
                      <v-select
                        :items="users"
                        v-model="editedItem.user_id"
                        item-text="name"
                        item-value="id"
                        label="User for import"
                      ></v-select>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="editedItem.tel"
                        label="ApiKey"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="6">
                      <v-select
                        multiple
                        :items="offices"
                        v-model="editedItem.office_id"
                        item-text="name"
                        item-value="id"
                        label="Office"
                      ></v-select>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="headline">Remove a supplier?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete"
                  >No</v-btn
                >
                <v-btn color="blue darken-1" text @click="deleteItemConfirm"
                  >Yes</v-btn
                >
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
      <template v-slot:item.report="{ item }">
        <statusesProvider :provider="item" />
      </template>

      <template v-slot:no-data>
        <v-btn color="primary" @click="getProvider"> Reset </v-btn>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import statusesProvider from "./statusProvider";
import axios from "axios";
export default {
  name: "providers",
  data: () => ({
    provider: {},
    dialog: false,
    dialogDelete: false,
    providers: [],
    users: [],
    headers: [
      { text: "Name", value: "name" },

      { text: "Edit", value: "actions", sortable: false },
      { text: "Report", value: "report", sortable: false },
    ],

    editedIndex: -1,
    editedItem: {
      name: "",
      password: "",
      active: 1,
      related_users_id: [],
      office_id: [],
      user_id: 0,
    },
    defaultItem: {
      name: "",
      password: "",
      active: 1,
      related_users_id: [],
      office_id: [],
      user_id: 0,
    },
    offices: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New supplier" : "Edit supplier";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  mounted() {
    // this.initialize(),
    this.getOffices();
    this.getUsers();
    this.getProvider();
  },

  methods: {
    getOffices() {
      let self = this;
      axios
        .get("/api/getOffices")
        .then((res) => {
          self.offices = res.data;
        })
        .catch((error) => console.log(error));
    },
    getUsers() {
      let self = this;
      axios
        .get("/api/users")
        .then((res) => {
          self.users = res.data;
        })
        .catch((error) => console.log(error));
    },
    report(item) {
      if (this.provider == item) {
        this.provider = {};
        return;
      }
      this.provider = item;
    },
    getProvider() {
      let self = this;
      axios
        .get("/api/providerall")
        .then((res) => {
          self.providers = res.data;
          self.providers = self.providers.map(function (p) {
            if (p.related_users_id.length > 0)
              p.related_users_id = JSON.parse(p.related_users_id);
            if (p.office_id.length > 0) p.office_id = JSON.parse(p.office_id);
            return p;
          });
        })
        .catch((error) => console.log(error));
    },
    saveProvider(provider) {
      let self = this;
      axios
        .post("/api/provider", provider)
        .then((res) => {
          if (provider.id == undefined) {
            let idx = self.providers.indexOf(provider);
            Object.assign(self.providers[idx], res.data.provider);
          }
        })
        .catch((error) => console.log(error));
    },
    editItem(item) {
      this.editedIndex = this.providers.indexOf(item);
      this.editedItem = Object.assign({}, item);
      if (!Array.isArray(item.related_users_id))
        this.editedItem.related_users_id = [];
      if (!Array.isArray(item.office_id)) this.editedItem.office_id = [];
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedIndex = this.providers.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      axios
        .delete("/api/provider/" + this.editedItem.id)
        .then((res) => {
          // console.log(res);
        })
        .catch((error) => console.log(error));
      this.providers.splice(this.editedIndex, 1);
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      if (this.editedIndex > -1) {
        this.saveProvider(this.editedItem);
        Object.assign(this.providers[this.editedIndex], this.editedItem);
      } else {
        this.saveProvider(this.editedItem);
        this.providers.push(this.editedItem);
      }
      this.close();
    },
  },
  components: {
    statusesProvider,
  },
};
</script>
