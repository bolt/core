const getters = { 
  getRecords: state => state.records,
  getOrder(state) {
    return state.records.map(record => {
      return record.id
    });
  },
}

export default getters