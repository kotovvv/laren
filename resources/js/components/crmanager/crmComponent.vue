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

    <v-main class="lighten-2">
      <v-container fluid>
        <!-- <v-row> -->
        <!-- table -->
        <component :user="$props.user" :is="managerComponent" />
        <!-- <tablenewlid></tablenewlid> -->
        <!-- </v-row> -->
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
const lids = () => import("../crmanager/lids.vue");
const lids3 = () => import("../crmanager/lids3.vue");
const mlids = () => import("../manager/mlids.vue");
const report = () => import("../manager/report.vue");

export default {
  props: ["user"],
  data: () => ({
    drawer: null,
    selectedItem: 0,
    managerMenu: "lids",
    items: [
      { text: "Distribution", name: "lids", icon: "mdi-account-arrow-left" },
      { text: "Distribution3", name: "lids3", icon: "mdi-sitemap" },
      { text: "Management", name: "mlids", icon: "mdi-phone-log-outline" },
      { text: "Reports", name: "report", icon: "mdi-timetable" },
    ],
  }),
  computed: {
    managerComponent() {
      if (this.managerMenu == "lids") return lids;
      if (this.managerMenu == "lids3") return lids3;
      if (this.managerMenu == "mlids") return mlids;
      if (this.managerMenu == "report") return report;
    },
  },
  methods: {},
};
</script>
