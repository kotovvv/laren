<template>
  <div>
    <v-row>
      <v-col v-if="!lead.docs_сompl">
        <v-sheet outlined class="pa-3">
          <v-text-field label="Description"></v-text-field>
          <v-file-input
            label="File input"
            @change="onFilePicked"
          ></v-file-input>
          <v-btn>Upload</v-btn>
        </v-sheet>
      </v-col>
      <v-col> Files </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  name: "Tierdoc",
  props: ["lead"],
  data() {
    return {
      tierUrl: "",
      tierFile: null,
      tierName: "",
    };
  },

  mounted() {},

  methods: {
    pickFile() {
      this.$refs.tier.click();
    },
    onFilePicked(e) {
      const files = e.target.files;
      if (files[0] !== undefined) {
        this.tierName = files[0].name;
        if (this.tierName.lastIndexOf(".") <= 0) {
          return;
        }
        const fr = new FileReader();
        fr.readAsDataURL(files[0]);
        fr.addEventListener("load", () => {
          this.tierUrl = fr.result;
          this.tierFile = files[0];
        });
      } else {
        this.tierName = "";
        this.tierFile = "";
        this.tierUrl = "";
      }
    },

    uploadFile() {
      let formData = new FormData();
      formData.append("tier_file", this.form.tierFile);

      axios
        .post("/specialties", formData)
        .then((res) => {})
        .catch((err) => {});
    },
  },
};
</script>
