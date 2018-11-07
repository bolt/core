const mutations = {
  setType(state, data){
    state.type = data
  },
  setRowSize(state, data){
    state.rowSize = data
  },
  setSorting(state, arg){
    state.sorting = arg
  }
}

export default mutations
