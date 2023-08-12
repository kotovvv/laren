<template>
  <v-app id="inspire">
    <v-app-bar app>
      <v-bottom-navigation
        color="primary"
        background-color="transparent"
        :value="managerMenu"
        style="box-shadow: none"
      >
        <v-btn
          :value="item.name"
          v-for="(item, i) in items"
          :key="i"
          @click="managerMenu = item.name"
        >
          <span>{{ item.text }}</span>

          <v-icon>{{ item.icon }}</v-icon>
        </v-btn>
      </v-bottom-navigation>

      <v-spacer></v-spacer>
      <div class="align-center mr-2">{{ userfio }}</div>

      <v-btn @click="$emit('login', {})">Logout</v-btn>
    </v-app-bar>

    <v-main>
      <v-container fluid>
        <!-- <v-row> -->
        <!-- table -->
        <component :user="user" :is="managerComponent" />
        <!-- <tablenewlid></tablenewlid> -->
        <!-- </v-row> -->
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
const mlids = () => import("./mlids.vue");
const report = () => import("./report.vue");

export default {
  props: ["user"],
  data: () => ({
    drawer: null,
    selectedItem: 0,
    managerMenu: "report",

    items: [
      { text: "Reports", name: "report", icon: "mdi-timetable" },
      { text: "Manager", name: "mlids", icon: "mdi-phone-log-outline" },
    ],
  }),
  computed: {
    managerComponent() {
      if (this.managerMenu == "mlids") return mlids;
      if (this.managerMenu == "report") return report;
    },
  },

  methods: {},
};
</script>
