<template>
  <div>
    <v-row>
      <v-col v-if="!lead.docs_сompl">
        <v-sheet outlined class="pa-3">
          <v-text-field label="Description" v-model="desc"></v-text-field>
          <v-file-input
            label="File input"
            @change="onFilePicked"
            v-model="file"
          ></v-file-input>
          <v-btn block :disabled="filedesc" @click="uploadFile">Upload</v-btn>
        </v-sheet>
      </v-col>
      <v-col> Files </v-col>
    </v-row>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "Tierdoc",
  props: ["lead"],
  data() {
    return {
      file: null,
      desc: "",
      fileType: "",
      fileName: "",
      fileSize: "",
    };
  },

  mounted() {},
  computed: {
    filedesc() {
      return this.file == null || this.desc == "" ? true : false;
    },
  },
  methods: {
    pickFile() {
      this.$refs.tier.click();
    },
    onFilePicked(e) {
      if (!this.file) {
        this.data = "No File Chosen";
      }
      const fr = new FileReader();
      fr.readAsDataURL(this.file);
      fr.addEventListener("load", () => {
        this.fileSize = this.file.size;
        this.fileName = this.file.name;
        this.fileType = this.file.type;
      });
    },

    uploadFile() {
      let formData = new FormData();
      formData.append("file", this.file);

      axios
        .post("/uploadDoc", formData)
        .then((res) => {})
        .catch((err) => {});
    },
  },
};
</script>
