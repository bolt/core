const actions = { 
  selectAll({ commit }, arg) {
    commit('selectAll', arg)
  },
  select({commit}, id) {
    commit('select', id)
  },
  deSelect({commit}, id) {
    commit('deSelect', id)
  }
}

export default actions