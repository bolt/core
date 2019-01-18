<template>
    <div>
        <div v-if="loading" class="row col">
            <p>Loading...</p>
        </div>

        <div v-else>
            <h3>Latest {{ type }}</h3>
            <table>
                <tbody>
                    <template v-for="record in records" class="row col">
                        <tr :key="record.id">
                            <td>{{ record.id }}</td>
                            <td><a :href="'edit/'+record.id">{{ record.fields[0].value.value }}</a></td>
                        </tr>
                        <!-- Maybe is better to have a component to print each row? -->
                        <!-- <Context :id="item.id" :key="item.id" :contentType="content.contentType" :title="item.fields[0]"></Context> -->
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import ContentAPI from "../../service/api/content";

export default {
  name: "context",
  props: ["type", "limit"],
  components: {
    // Context
  },
  data() {
    return {
      message: "",
      loading: true,
      records: []
    };
  },
  created() {
    this.records = ContentAPI.getRecords(this.type);

    ContentAPI.fetchRecords(this.type)
      .then(records => {
        this.records = records;
      })
      .catch(error => console.log(error))
      .finally(() => {
        this.loading = false;
      });
  }
};
</script>
