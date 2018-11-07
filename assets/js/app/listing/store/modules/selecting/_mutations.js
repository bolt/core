const mutations = {
  selectAll(state, arg){
    state.selectAll = arg
  },
  select(state, id){
    state.selectedCount++
    state.selected.push(id);
  },
  deSelect(state, id){
    state.selectedCount--
    let index = state.selected.indexOf(id);
    if (index > -1)
      state.selected.splice(index, 1);
  }
}

export default mutations
