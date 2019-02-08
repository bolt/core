<template>
  <div>
    <div v-if="loading" class="row col"><p>Loading...</p></div>

    <div v-else>
      <h3>Latest {{ type }}</h3>
      <table>
        <tbody>
          <template v-for="record in records" class="row col">
            <tr :key="record.id">
              <td>{{ record.id }}</td>
              <td>
                <a :href="record.extras.editLink">{{ record.extras.title }}</a>
              </td>
            </tr>
            <!-- Maybe is better to have a component to print each row? -->
            <!-- <Context :id="item.id" :key="item.id" :contenttype="content.contenttype" :title="item.fields[0]"></Context> -->
          </template>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { getRecords, fetchRecords } from './../../../../services/api/content';

export default {
  name: 'Context',
  components: {
    // Context
  },
  props: ['type', 'limit'],
  data() {
    return {
      message: '',
      loading: true,
      records: [],
    };
  },
  created() {
    this.records = getRecords(this.type);

    fetchRecords(this.type)
      .then(records => {
        this.records = records;
      })
      .catch(error => console.log(error))
      .finally(() => {
        this.loading = false;
      });
  },
};
</script>
