const actions = { 
  setType({ commit }, data) {
    commit('setType', data)
  },
  setRowSize({ commit }, data) {
    commit('setRowSize', data)
  },
  setSorting({ commit }, arg) {
    commit('setSorting', arg)
  },
}

export default actions