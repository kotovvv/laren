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
    getDocs() {
      const self = this;
      axios
        .get("/api/getDocs?lead_id=" + self.lead.id)
        .then((res) => {
          console.log(res);
          if (res.status == 200) {
          }
        })
        .catch((err) => {});
    },
    uploadFile() {
      const self = this;
      let formData = new FormData();
      formData.append("file", this.file);
      formData.append("fileSize", this.file.size);
      formData.append("fileName", this.file.name);
      formData.append("fileType", this.file.type);
      formData.append("lead_id", this.lead.id);
      formData.append("desc", this.desc);
      axios
        .post("/api/uploadDoc", formData)
        .then((res) => {
          console.log(res);
          if (res.status == 200) {
            self.file = null;
            self.desc = "";
            self.fileType = "";
            self.fileName = "";
            self.fileSize = "";
            getDocs();
          }
        })
        .catch((err) => {});
    },
  },
};
</script>
